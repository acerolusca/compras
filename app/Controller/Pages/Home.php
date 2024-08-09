<?php


namespace App\Controller\Pages;

use \App\Utils\View;



class Home extends Page {

    /**
     * Método responável por retornar a view de usuários
     *
     * @return string
     */
    public static function render(){

        //VIEW DE NOTÍCIAS
        $content = View::render("pages/home/index");

        //VIEW DA PÁGINA
        return parent::getPage("Início", $content);

    }


}






