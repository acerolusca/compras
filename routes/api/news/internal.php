<?php

use \App\Controller\Api\NewsApi;
use \App\Core\Response;


/**
 * @var \App\Core\Router $obRouter
 */



//ROTA PARA BUSCAR AS INFORMAÇÕES DE TODAS AS NOTÍCIAS
$obRouter->get("/news/all",[
    "middlewares" => [
        "required-login",
    ],
    function(){
        return new Response(200, NewsApi::getAll(), "application/json");
    }
]);



//ROTA PARA CADASTRO DE NOTÍCIA
$obRouter->post("/news/register",[
    "middlewares" => [
        "required-login"
    ],
    function($request){
        return new Response(200, NewsApi::register($request), "application/json");
    }
]);





//ROTA PARA BUSCAR AS INFORMAÇÕES DE UMA NOTÍCIA PELO ID
$obRouter->get("/news/info/{id}",[
    "middlewares" => [
        "required-login"
    ],
    function($id){
        return new Response(200, NewsApi::getInfo($id), "application/json");
    }
]);




//ROTA PARA EDITAR AS INFORMAÇÕES DE UMA NOTÍCIA
$obRouter->post("/news/edit",[
    "middlewares" => [
        "required-login"
    ],
    function($request){
        return new Response(200, NewsApi::edit($request), "application/json");
    }
]);



//ROTA PARA DELETAR AS INFORMAÇÕES DE UMA NOTÍCIA PELO ID
$obRouter->post("/news/delete/{id}",[
    "middlewares" => [
        "required-login"
    ],
    function($id){
        return new Response(200, NewsApi::delete($id), "application/json");
    }
]);




//ROTA PARA MUDAR O STATUS DE DESTAQUE DE UMA NOTÍCIA NOTÍCIA
$obRouter->post("/news/change-highlighted",[
    "middlewares" => [
        "required-login"
    ],
    function($request){
        return new Response(200, NewsApi::changeHighlighted($request), "application/json");
    }
]);




//ROTA PARA MUDAR O STATUS DE VISIBILIDADE DE UMA NOTÍCIA NOTÍCIA
$obRouter->post("/news/change-visible",[
    "middlewares" => [
        "required-login"
    ],
    function($request){
        return new Response(200, NewsApi::changeVisible($request), "application/json");
    }
]);




//ROTA PARA SALVAR UMA IMAGEM CARREGADA NO EDITOR DE NOTÍCIAS
$obRouter->post("/news/upload-editor-image",[
    "middlewares" => [
        "required-login"
    ],
    function(){
        return new Response(200, NewsApi::uploadEditorImage(), "application/json");
    }
]);






































