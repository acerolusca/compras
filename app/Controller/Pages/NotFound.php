<?php

namespace App\Controller\Pages;

use \App\Utils\View;

class NotFound {

    /**
     * Método responsável por retonar página não encontrada
     *
     * @return string
     */
    public static function render() {
        return View::render("pages/error/index", [
            "code" => 404,
            "message" => "Página não encontrada"
        ]);
    }
}