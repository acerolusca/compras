<?php

namespace App\Controller\Pages;


use \App\Utils\View;


class ApiDocs {
    /**
     * Método responsável por retornar a página de documentação das API's
     * @return string
     */
    public static function render(){
        return View::render("pages/../../../public/api/index");
    }
}