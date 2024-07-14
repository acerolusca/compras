<?php


namespace App\Controller\Pages;

use \App\Utils\View;
use App\Service\UserService;
use App\Database\Database;
use App\Repository\UserRepository;
use App\Model\User;
use \Exception;


class Page
{


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
     * Método responsável por retornar a topbar da página genérica
     *
     * @return string
     */
    private static function getTopbar()
    {

        $user = new User();
        
        try {
            //RETORNA O CONTEÚDO GERAL DA TOPBAR
            return View::render("components/topbar/index", [
                "username" => $user->getLoggedInfo()["username"] ?? "",
                "userImagePath" => $user->getLoggedInfo()["imagePath"] ?? "" 
            ]);


        } catch (Exception $e) {

            //LIMPA O CONTEÚDO DOS ITEMS DA TOPBAR CASO ALGUM ERRO OCORRA 
            return View::render("components/topbar/index", [
                "username" => "",
                "userImagePath" => ""
            ]);
        }
    }




    /**
     * Método responsável por retornar o contéudo da sidebar da página genérica
     *
     * @return string
     */
    private static function getSidebar()
    {
        $user = new User();

        try {
            $administratorItems = $user->getSessionPrivilege() == "administrator" ? View::render("components/sidebar/administrator-items") : "";
            return View::render("components/sidebar/index", [
                "administratorItems" => $administratorItems
            ]);
        } catch (Exception $e){
            return View::render("components/sidebar/index", [
                "administratorItems" => ""
            ]);
        }


    }




    /**
     * Método responsável por retornar o conteúdo da página genérica
     *
     * @return string
     */
    public static function getPage($title, $content)
    {
        return View::render("pages/index", [
            "title" => $title,
            "content" => $content,
            "sidebar" => self::getSidebar(),
            "topbar" => self::getTopbar()
        ]);
    }
}
