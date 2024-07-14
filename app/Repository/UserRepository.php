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
                "registerDate" => $user->getRegisterDate()
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
}
