<?php

namespace App\Service;

use App\Model\User;
use App\Repository\UserRepository;
use App\Utils\View;
use App\Communication\Email;

use \App\Session\Session;
use \Exception;
use \DateTime;


class UserService
{


    /**
     * Instância de UserRepository
     *
     * @var UserRepository
     */
    private UserRepository $userRepository;


    /**
     * Construtor da classe
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }





    /**
     * Método responsável pelo login de um usuário
     *
     * @param array  $userData
     * @return void
     */
    public function login(array $userData)
    {
        try {

            $identifier = $userData["identifier"] ?? "";
            $password = $userData["password"] ?? "";

            $statement = $this->userRepository->getByIdentifier($identifier);

            if ($statement->rowCount() == 0) {
                throw new Exception("Credenciais inválidas", 401);
            }


            $user = $statement->fetchObject(User::class);


            if (!password_verify($password, $user->getPassword())) {
                throw new Exception("Credenciais inválidas", 401);
            }

            $user->login();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }




    
    /**
     * Método responsável pelo cadastro de um usuário
     *
     * @param array $userData
     * @return string
     */
    public function register(array $userData): string
    {
        try {

            $user = new User();

            $user->setFullname($userData["registerFullname"] ?? "");
            $user->setUsername($userData["registerUsername"] ?? "");
            $user->setEmail($userData["registerEmail"] ?? "");
            $user->setCpf($userData["registerCpf"] ?? "");
            $user->setDefaultImagePath();
            $user->setPrivilege("editor");
            $user->setRegisterDate((new DateTime())->format("Y-m-d H:i:s"));
            $randomPassword = $user->setRandomPassword();


            self::sendRegisterEmail(
                $user->getFullname(), 
                $user->getEmail(), 
                $user->getCpf(), 
                $randomPassword
            );


            $this->userRepository->insert($user);

            return $randomPassword;

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }





    /**
     * Método responsável por retornar uma string (HTML) com o corpo da tabela de usuários (não administradores)
     *
     * @return string
     */
    public function getNoAdministratorsUsersTableBody(): string
    {

        try {

            $statement = $this->userRepository->getAll();

            if ($statement->rowCount() == 0) {
                return "";
            }

            $usersList = "";


            while ($user = $statement->fetchObject(User::class)) {

                if($user->getPrivilege() != "administrator") {
                    $usersList .= View::render("pages/users/user-item", [
                        "imagePath" => $user->getImagePath(),
                        "fullname" => $user->getFullname(),
                        "email" => $user->getEmail(),
                        "cpf" => $user->getCpf(),
                        //"registerDate" => (new DateTime($user->getRegisterDate()))->format("d/m/Y H:i:s")
                    ]);
                }

            }

            return $usersList;
        } catch (Exception $e) {
            return "";
        }
    }




    /**
     * Método responsável por gerar uma string randômica 
     *
     * @param integer $length
     * @return string
     */
    private static function str_rand(int $length = 64): string
    {
        $length = ($length < 4) ? 4 : $length;
        return bin2hex(random_bytes(($length - ($length % 2)) / 2));
    }





    /**
     * Método responsável por enviar email ao usuário com a senha de cadastro
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @return void
     */
    private static function sendRegisterEmail(string $name, string $email, string $cpf, string $password)
    {

        try {

            $body = View::render("pages/users/register-email", [
                "name" => $name,
                "email" => $email,
                "cpf" => $cpf,
                "password" => $password
            ]);


            $subject = "CADASTRO NO SISTEMA DE GERENCIAMENTO COMPRAS-MA";


            $objEmail = new Email();
            $success = $objEmail->sendEmail($email, $subject, $body);

            if (!$success) {
                throw new Exception(
                    $objEmail->getError(),//"Problema ao enviar email de confirmação de cadastro. Por favor, tente novamente mais tarde", 
                    400
                );
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}
