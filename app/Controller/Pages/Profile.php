<?php


namespace App\Controller\Pages;


use App\Model\User;
use App\Service\UserService;
use App\Repository\UserRepository;

use App\Database\Database;
use \Exception;

use \App\Utils\View;



class Profile extends Page {



    /**
     * Instância de UserService Service
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
     * Método responsável por buscar as informações do usuário e retorná-las em um array
     * @return array
     */
    private static function getUserInfo() {

        try{
            self::initialize();

            $user = new User();
            $user->setLoggedInfo();

            $userCpf = $user->getCpf();


            if(empty($userCpf)) {
                return [];
            }

            return self::$userService->getInfo($userCpf);


        } catch(Exception $e){
            return [];
        }

    }



    /**
     * Método responável por retornar a página de perfil do usuário
     *
     * @return string
     */
    public static function render() {


        $args = self::getUserInfo();

        
        if(count($args) == 0) {
            return View::render("pages/error/index", [
                "code" => 404, 
                "message" => "Página não encontrada!"
            ]);
        }


        $content = View::render("pages/profile/index", $args);

        return parent::getPage("Perfil", $content);

    }


}











