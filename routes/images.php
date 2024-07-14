<?php

use \App\Controller\Images;
use \App\Core\Response;


// ROTA PARA IMAGENS DE USUÁRIOS
$obRouter->get("/image/user/{relativeImagePath}", [
    function ($relativeImagePath) {
       return Images\UserImage::getImage($relativeImagePath);
    }
]);