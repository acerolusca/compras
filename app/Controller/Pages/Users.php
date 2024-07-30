<?php


namespace App\Controller\Pages;

use App\Service\UserService;
use App\Repository\UserRepository;

use App\Database\Database;
use \Exception;

use \App\Utils\View;



class Users extends Page {

    /**
     * Instância de UserService
     *
     * @var UserService|null
     */
    private static ?UserService $userService = null;





    /**
     * Método responsável por inicializar o UserService estaticamente
     * 
     * @return void
     */
    public static function initialize(): void
    {
        if (self::$userService === null) {
            $db = new Database("user");
            $userRepository = new UserRepository($db);

            self::$userService = new UserService($userRepository);
        }
    }




    /**
     * Método responsável por retornar uma string (HTML) com o formulário de cadastro de usuário
     *
     * @return string
     */
    private static function getRegisterForm() {
        return View::render("pages/users/register-form",);
    }




    /**
     * Método responsável por retornar uma string (HTML) com o formulário de edição de usuário
     *
     * @return string
     */
    private static function getEditForm() {
        return View::render("pages/users/edit-form",);
    }






    /**
     * Método responsável por retornar uma string (HTML) com o corpo da tabela de usuários (não administradores)
     * @return string
     */
    private static function getNoAdministratorsUsersTableBody() {

        try{
            self::initialize();

            return self::$userService->getNoAdministratorUsersTableBody();
            
        }catch(Exception $e){
            return "";
        }
    }




    /**
     * Método responável por retornar a página de usuários
     *
     * @return string
     */
    public static function render(){

        //VIEW DE USUÁRIOS
        $content = View::render("pages/users/index", [
            "noAdministratorsUsersTableBody" => self::getNoAdministratorsUsersTableBody(),
            "registerForm" => self::getRegisterForm(),
            "editForm" => self::getEditForm()
        ]);

        //VIEW DA PÁGINA
        return parent::getPage("Usuários", $content);

}



}






