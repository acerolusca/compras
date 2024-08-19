<?php

namespace App\Controller\Pages;


use \App\Utils\View;


class Preview {
    /**
     * Método responsável por retornar a página de preview de uma notícia
     * @return string
     */
    public static function render(){
        return View::render("pages/preview/index");
    }
}