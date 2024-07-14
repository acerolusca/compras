<?php


namespace App\Controller\Pages;

use App\Service\NewsService;
use App\Repository\NewsRepository;

use App\Database\Database;
use \Exception;

use \App\Utils\View;



class News extends Page {

    /**
     * Instância de NewsService
     *
     * @var NewsService|null
     */
    private static ?NewsService $newsService = null;



    /**
     * Método responsável por inicializar o NewsService estaticamente
     * 
     * @return void
     */
    public static function initialize(): void
    {
        if (self::$newsService === null) {
            $db = new Database("news");
            $newsRepository = new NewsRepository($db);

            self::$newsService = new NewsService($newsRepository);
        }
    }



    /**
     * Método responsável por buscar a view de cadastro de notícia
     *
     * @return string
     */
    private static function getRegisterForm() {
        return View::render("pages/news/register-form",);
    }



    /**
     * Método responsável por buscar a view de edição de notícia
     *
     * @return string
     */
    public static function getEditForm() {
        return View::render("pages/news/edit-form");
    }






    /**
     * Método responsável por retornar a lista de todos os usuários
     * @return string
     */
    private static function getUsersList() {

        try{
            self::initialize();

            return self::$userService->getAll();
            
        }catch(Exception $e){
            return "";
        }
    }




    /**
     * Método responável por retornar a view de usuários
     *
     * @return string
     */
    public static function render(){

        //VIEW DE ADMINISTRADORES
        $content = View::render("pages/users/index", [
            "usersList" => self::getUsersList(),
            "registerForm" => self::getRegisterForm()
        ]);

        //VIEW DA PÁGINA
        return parent::getPage("Usuários", $content);

}



}






