<?php

use App\Controller\Api\ExternalNewsApi;
use App\Core\Response;


/**
 * @var \App\Core\Router $obRouter
 */



//ROTA PARA BUSCAR AS INFORMAÇÕES BÁSICAS DE TODAS AS NOTÍCIAS DISPONÍVEIS
$obRouter->get("/api/news/all",[
    "middlewares" => [
        "required-website-origin",
    ],
    function(){
        return ExternalNewsApi::getAllAvailable();
    }
]);





//ROTA PARA BUSCAR AS INFORMAÇÕES BÁSICAS DAS NOTÍCIAS DESTAQUE
$obRouter->get("/api/news/highlighted",[
    "middlewares" => [
        "required-website-origin",
    ],
    function(){
        return ExternalNewsApi::getHighlightedAvailable();
    }
]);





//ROTA PARA BUSCAR AS INFORMAÇÕES BÁSICAS DAS NOTÍCIAS DESTAQUE
$obRouter->get("/api/news/regular",[
    "middlewares" => [
        "required-website-origin",
    ],
    function(){
       return ExternalNewsApi::getRegularAvailable();
    }
]);




//ROTA PARA BUSCAR AS INFORMAÇÕES DE UMA NOTÍCIA DISPONÍVEL PELO ID
$obRouter->get("/api/news/info/{id}",[
    "middlewares" => [
        "required-website-origin",
    ],
    function($id){
       return ExternalNewsApi::getInfoAvailable($id);
    }
]);




//ROTA PARA BUSCAR NOTÍCIAS DE ACORDO COM ALGUM PARÂMETRO
$obRouter->get("/api/news/search/{searched}",[
    "middlewares" => [
        "required-website-origin",
    ],
    function($searched){
        return ExternalNewsApi::search($searched);
    }
]);





