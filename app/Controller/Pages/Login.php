<?php

namespace App\Controller\Pages;


use \App\Utils\View;


class Login {
    /**
     * Método responsável por retornar a página de login do usuário
     *
     * @return string
     */
    public static function render(){
        return View::render("pages/login/index");
    }
}