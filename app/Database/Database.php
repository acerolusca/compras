<?php


namespace App\Database;


use Exception;
use PDOException;
use PDOStatement;
use PDO;



class Database
{

    /**
     * Host de conexão com o banco de dados
     * @var string
     */
    private static string $host;


    /**
     * Nome do banco de dados
     * @var string
     */
    private static string $name;


    /**
     * Usuário do banco de dados
     * @var string
     */
    private static string $user;


    /**
     * Senha do banco de dados
     * @var string
     */
    private static string $pass;


    /**
     * Porta de conexão com  o banco de dados
     * @var integer
     */
    private static int $port;


    /**
     * Nome da tabela a ser manipulada
     *
     * @var string
     */
    private string $table;


    /**
     * Instância de connexão com o banco de dados
     *
     * @var PDO
     */
    private PDO $connection;



    /**
     * Método responsável por configurar a classe
     * @param  string  $host
     * @param  string  $name
     * @param  string  $user
     * @param  string  $pass
     * @param  integer $port
     */
    public static function config($host, $name, $user, $pass, $port = 3306)
    {
        self::$host = $host;
        self::$name = $name;
        self::$user = $user;
        self::$pass = $pass;
        self::$port = $port;
    }




    /**
     * Define a tablea e instancia a conexão
     *
     * @param string $table
     */
    public function __construct($table = null)
    {
        $this->table = $table;
        $this->setConnection();
    }



    /**
     * Método responsável por criar uma connexão com o banco de dados
     *
     * @return void
     */
    private function setConnection()
    {
        try {
            $this->connection = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$name . ";port=" . self::$port, self::$user, self::$pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Erro na conexão", $e->getCode());
        }
    }



    /**
     *  Método responsável por iniciar uma nova transação
     *
     * @return void
     */
    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }



    /**
     * Método responsável por confirmar todas as operações realizadas desde o início da transação 
     *
     * @return void
     */
    public function commit()
    {
        $this->connection->commit();
    }


    /**
     * Método responsável por reverter todas as operações realizadas desde o início da transação
     *
     * @return void
     */
    public function rollBack()
    {
        $this->connection->rollBack();
    }



    /**
     * Método responsável por executar as queries dentro do banco de dados
     *
     * @param string $query
     * @param array $params
     * @return PDOStatement
     */
    public function execute($query, $params = [])
    {

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
            return $statement;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(),intval($e->getMessage()));
        }
    }



    /**
     * Método responsável por inserir dados no banco
     * @param array $fields | field => value |
     * @return integer ID inserido
     */
    public function insert(array $fields): int
    {

        try {

            //DADOS DA QUERY
            $keys = array_keys($fields);
            $binds = array_pad([], count($keys), "?");



            //MONTA A QUERY
            $query = "INSERT INTO $this->table (" . implode(",", $keys) . ") VALUES (" . implode(",", $binds) . ")";
            $this->execute($query, array_values($fields));





            //RETORNA O ÚLTIMO ID INSERIDO
            return $this->connection->lastInsertId();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por recuperar dados do banco
     *
     * @param array $fields
     * @param array $join [["table" => $table, "reference" => $reference, "referenced" => $referenced]]
     * @param array $where ["columun = " => $value]
     * @param string $order
     * @param string $limit
     * @return PDOStatement
     */
    public function select(array $fields = ["*"],  array $join = [], array $where = [], string $order = "", string $limit = "", string $group = ""): PDOStatement
    {

        try {


            //COLUNAS
            $fields = implode(", ", $fields);


            //WHERE
            if (count($where) > 0) {
                $whereKeys = array_keys($where);
                $whereKeys = array_map(function ($key) {
                    return $key . "?";
                }, $whereKeys);

                $binds = array_values($where);
                $where = "WHERE " . implode(" ", $whereKeys);
            } else {
                $binds = [];
                $where = "";
            }


            //JOIN
            if (count($join) > 0) {
                $joinConstruct = "";
                foreach ($join as $params) {
                    $joinConstruct .= "JOIN " . $params["table"] . " ON " . $params["referencing"] . " = " . $params["referenced"] . " ";
                }
                $join = $joinConstruct;
            } else {
                $join = "";
            }


            //ORDER
            $order = strlen($order) > 0 ? "ORDER BY $order" : "";


            //LIMIT 
            $limit = strlen($limit) > 0 ? "LIMIT $limit" : "";


            //GROUP BY
            $group = strlen($group) > 0 ? "GROUP BY $group" : ""; 


            //MONTA A QUERY
            $query = "SELECT $fields FROM $this->table $join $where $group $order $limit";


            //RETORNA UMA INSTANCIA DE PDOSTATEMENT
            return $this->execute($query, array_values($binds));
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por atualizar dados no banco
     *
     * @param array $sets ["column" => $value]
     * @param array $where ["column = " => $value]
     * @return boolean
     */
    public function update(array $sets = ["id" => "id"], array $where = ["id = " => "id"]): bool
    {

        try {

            //SETS
            $setsKeys = array_keys($sets);
            $setsKeys = array_map(function ($key) {
                return "$key = ?";
            }, $setsKeys);

            $binds = array_values($sets);
            $sets = "SET " . implode(", ", $setsKeys);



            //WHERE
            $whereKeys = array_keys($where);
            $whereKeys = array_map(function ($key) {
                return $key . "?";
            }, $whereKeys);

            $binds = array_merge($binds, array_values($where));
            $where = "WHERE " . implode(" ", $whereKeys);


            //MONTA A QUERY
            $query = "UPDATE $this->table $sets $where";



            //RETORNA UM BOOLEANO QUE INDICA SE A ATUALIZAÇÂO FOI REALIZADA
            return $this->execute($query, array_values($binds))->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por remover dados do banco 
     *
     * @param array $where
     * @return boolean
     */
    public function delete(array $where = ["id = " => "id"]): bool
    {

        try {

            //WHERE
            $whereKeys = array_keys($where);
            $whereKeys = array_map(function ($key) {
                return $key . "?";
            }, $whereKeys);

            $binds = array_values($where);
            $where = "WHERE " . implode(" ", $whereKeys);


            //MONTA A QUERY
            $query = "DELETE FROM $this->table $where";


            //RETORNA UM BOOLEANO QUE INDICA SE A ATUALIZAÇÂO FOI REALIZADA
            return $this->execute($query, array_values($binds))->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro na exclusão!", $e->getCode());
        }
    }
}
