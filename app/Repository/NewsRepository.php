<?php

namespace App\Repository;

use App\Model\News;
use App\Database\Database;
use \Exception;
use PDOStatement;

class NewsRepository
{


    /**
     * Instância de Database
     *
     * @var Database
     */
    private Database $db;




    /**
     * Construtor da classe
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }



    /**
     * Método responsável por inserir uma notícia no banco de dados
     *
     * @param News $user
     * @return integer
     */
    public function insert(News $news): int
    {
        try {
            $fields = [
                "title" => $news->getTitle(),
                "summary" => $news->getSummary(),
                "body" => $news->getBody(),
                "imagePath" => $news->getImagePath(),
                "author" => $news->getauthor(),
                "registerDate" => $news->getRegisterDate(),
                "schedulingDate" => $news->getSchedulingDate(),
                "highlighted" => $news->getHighlighted(),
                "visible" => $news->getVisible()
            ];

            return $this->db->insert($fields);

        } catch (Exception $e) {
            throw new Exception("Erro ao cadastrar notícias.", $e->getCode());
        }
    }



    /**
     * Método responsável por buscar as informações de todas as notícias
     *
     * @return PDOStatement
     */
    public function getAll(): PDOStatement
    {
        try {
            return $this->db->select(["*"], [], [], "schedulingDate DESC");
        } catch (Exception $e) {
            throw new Exception("Erro ao carregar notícias.", $e->getCode());
        }
    }



    /**
     * Método responsável por buscar informações de uma notícia pelo ID
     *
     * @param string $id
     * @return PDOStatement
     */
    public function getById(string $id): PDOStatement
    {
        try {

            $where = ["id = " => $id];

            return $this->db->select(["*"], [], $where);
        } catch (Exception $e) {
            throw new Exception("Erro ao carregar notícia!", $e->getCode());
        }
    }



    /**
     * Método responsável por atualizar as informações de uma notícia no banco
     *
     * @param News $newsEdited
     * @param string $newsId
     * @return boolean
     */
    public function update(News $newsEdited, string $newsId): bool {
        try {

            $sets = [
                "title" => $newsEdited->getTitle(),
                "summary" => $newsEdited->getSummary(),
                "body" => $newsEdited->getBody(),
                "imagePath" => $newsEdited->getImagePath(),
                "schedulingDate" => $newsEdited->getSchedulingDate(),
                "highlighted" => $newsEdited->getHighlighted(),
                "visible" => $newsEdited->getVisible(),
                //"author" => $newsEdited->getauthor(),
                //"registerDate" => $newsEdited->getRegisterDate(),
            ];

            $where = ["id = " => $newsId];

            return $this->db->update($sets, $where);  

        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar notícia.", $e->getCode());
        }
    }



    /**
     * Método responsável por deletar as informações de uma notícia no banco
     *
     * @param string $newsId
     * @return boolean
     */
    public function delete(string $newsId): bool {
        try {

            $where = ["id = " => $newsId];

            return $this->db->delete($where);   
        } catch (Exception $e) {
            throw new Exception("Erro ao excluir notícia.", $e->getCode());
        }
    }


    /**
     * Método responsável por buscar notícias no banco de acordo com algum parâmetro
     *
     * @param string $data
     * @return PDOStatement
     */
    public function search(string $data): PDOStatement {
        try {

            $where = [
                "title LIKE " => "%$data%",
                "OR summary LIKE " => "%$data%",
                "OR DATE_FORMAT(`schedulingDate`, '%d/%m/%Y %H:%i:%s') LIKE " => "%$data%",
            ];

            return $this->db->select(["*"], [], $where, "schedulingDate DESC");   
        } catch (Exception $e) {
            throw new Exception("Erro ao carregar notícias.", $e->getCode());
        }
    }


    
    /**
     * Método responsável por verificar se o valor de determinada coluna é unica
     *
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function isUnique(string $key, mixed $value): bool{
        try {

            $fields = [$key];

            $where = ["$key = " => $value];

            $statement = $this->db->select($fields, [], $where);

            return $statement->rowCount() == 0;

        } catch (Exception $e) {   
            throw new Exception("Erro ao validar dados da notícia", $e->getCode());
         }
    }


}
