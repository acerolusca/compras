<?php

use \App\Controller\Images;

/**
 * @var \App\Core\Router $obRouter
 */


// ROTA PARA IMAGENS DE USUÁRIOS
$obRouter->get("/image/user/{imageName}", [
    "middlewares" => [
        "required-login",
    ],
    function ($imageName) {
       return Images\UserImage::getImage($imageName);
    }
]);



// ROTA PARA IMAGENS DE NOTÍCIAS
$obRouter->get("/image/news/{imageName}", [
    function ($imageName) {
       return Images\NewsImage::getImage($imageName);
    }
]);



// ROTA PARA IMAGENS CARREGADAS NO EDITOR DE NOTÍCIA
$obRouter->get("/image/editor/{imageName}", [
    function ($imageName) {
       return Images\EditorImage::getImage($imageName);
    }
]);