<?php

use \App\Controller\Images;
use \App\Core\Response;


// ROTA PARA IMAGENS DE USUÁRIOS
$obRouter->get("/image/user/{relativeImagePath}", [
    function ($relativeImagePath) {
       return Images\UserImage::getImage($relativeImagePath);
    }
]);



// ROTA PARA IMAGENS DE NOTÍCIAS
$obRouter->get("/image/news/{relativeImagePath}", [
    function ($relativeImagePath) {
       return Images\NewsImage::getImage($relativeImagePath);
    }
]);



// ROTA PARA IMAGENS CARREGADAS NO EDITOR DE NOTÍCIA
$obRouter->get("/image/editor/{relativeImagePath}", [
    function ($relativeImagePath) {
       return Images\EditorImage::getImage($relativeImagePath);
    }
]);