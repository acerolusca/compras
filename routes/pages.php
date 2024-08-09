<?php


use \App\Controller\Pages;
use \App\Core\Response;



/**
 * @var \App\Core\Router $obRouter
 */


//ROTA PARA LOGIN DE USUÁRIO
$obRouter->get("/",[
    "middlewares" => [
        "requireLogout",
    ],
    function(){
        return new Response(200, Pages\Login::render());
    }
]);




//ROTA PARA HOME DO SISTEMA
$obRouter->get("/home",[
    "middlewares" => [
        "requireLogin",
    ],
    function(){
        return new Response(200, Pages\Home::render());
    }
]);




//ROTA PARA VIEW DE NOTÍCIAS
$obRouter->get("/news",  [
    "middlewares" => [
        "requireLogin",
    ],
    function (){
    return new Response(200, Pages\News::render());
}]);




//ROTA PARA VIEW DE USUÁRIOS
$obRouter->get("/users",[
    "middlewares" => [
        "requireLogin",
        "requireAdministrator"
    ],
    function(){
        return new Response(200, Pages\Users::render());
    }
]);




//ROTA PARA VIEW DE PERFIL DE USUÁRIO
$obRouter->get("/profile",[
    "middlewares" => [
        "requireLogin",
    ],
    function(){
        return new Response(200, Pages\Profile::render());
    }
]);




//ROTA PARA LOGOUT DE USUÁRIO
$obRouter->get("/logout",[
    "middlewares" => [
        "requireLogin",
    ],
    function($request){
        return new Response(200, Pages\Logout::render($request));
    }
]);




//ROTA PARA PÁGINA NÃO ENCONTRADA
$obRouter->get("/not-found",  [
    function () {
        return new Response(200, Pages\NotFound::render());
    }
]);














