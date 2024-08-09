<?php

namespace App\Repository;

use App\Model\User;
use App\Database\Database;
use \Exception;
use PDOStatement;

class UserRepository
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
     * Método responsável por inserir um usuário no banco de dados
     *
     * @param User $user
     * @return integer
     */
    public function insert(User $user): int
    {

        try {
            $fields = [
                "fullname" => $user->getFullname(),
                "cpf" => $user->getCpf(),
                "username" => $user->getUsername(),
                "email" => $user->getEmail(),
                "password" => $user->getPassword(),
                "imagePath" => $user->getImagePath(),
                "privilege" => $user->getPrivilege(),
                "registerDate" => $user->getRegisterDate(),
                "firstAccess" => $user->getFirstAccess()
            ];

            return $this->db->insert($fields);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por buscar as informações de todos os usuários
     *
     * @return PDOStatement
     */
    public function getAll(): PDOStatement
    {
        try {
            return $this->db->select(["*"], [], [], "registerDate DESC");
        } catch (Exception $e) {
            throw new Exception("Erro ao carregar usuários!", $e->getCode());
        }
    }



    /**
     * Método responsável por buscar informações de um usuário pelo seu identificador (CPF ou Email)
     *
     * @param string $identifier
     * @return PDOStatement
     */
    public function getByIdentifier(string $identifier): PDOStatement
    {
        try {

            $where = [
                "cpf = " => $identifier,
                "OR email = " => $identifier
            ];

            return $this->db->select(["*"], [], $where);
        } catch (Exception $e) {
            throw new Exception(
                "Erro ao carregar usuário!", //$e->getMessage()
                $e->getCode()
            );
        }
    }



    /**
     * Método responsável por buscar informações do usuário pelo CPF
     *
     * @param string $cpf
     * @return PDOStatement
     */
    public function getByCpf(string $cpf): PDOStatement
    {
        try {
            $where = ["cpf = " => $cpf];
            return $this->db->select(["*"], [], $where);
        } catch (Exception $e) {
            throw new Exception("Erro ao carregar usuário!", $e->getCode());
        }
    }




    /**
     * Método responsável por atualizar as informações de um usuário no banco
     *
     * @param User $userEdited
     * @param string $lastCpf
     * @return boolean
     */
    public function update(User $userEdited, string $lastCpf): bool {
        try {

            $sets = [
                "fullname" => $userEdited->getFullname(),
                "cpf" => $userEdited->getCpf(),
                "username" => $userEdited->getUsername(),
                "email" => $userEdited->getEmail(),
                "password" => $userEdited->getPassword(),
                "imagePath" => $userEdited->getImagePath(),
                //"privilege" => $userEdited->getPrivilege(),
                //"registerDate" => $userEdited->getRegisterDate(),
                "firstAccess" => $userEdited->getFirstAccess()
            ];

            $where = ["cpf = " => $lastCpf];

            return $this->db->update($sets, $where);  

        } catch (Exception $e) {
            throw new Exception(
                "Erro ao atualizar usuário", 
                $e->getCode()
            );
        }
    }



    /**
     * Método responsável por deletar as informações de um usuário no banco
     *
     * @param string $cpf
     * @return boolean
     */
    public function delete(string $cpf): bool {
        try {

            $where = ["cpf = " => $cpf];

            return $this->db->delete($where);   
        } catch (Exception $e) {
            throw new Exception("Erro ao excluir usuário.", $e->getCode());
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
            throw new Exception("Erro ao validar dados do usuário", $e->getCode());
         }
    }


}
