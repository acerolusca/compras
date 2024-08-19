<?php


use \App\Controller\Pages;
use \App\Core\Response;



/**
 * @var \App\Core\Router $obRouter
 */


//ROTA PARA LOGIN DE USUÁRIO
$obRouter->get("/",[
    "middlewares" => [
        "required-logout",
    ],
    function(){
        return new Response(200, Pages\Login::render());
    }
]);




//ROTA PARA HOME DO SISTEMA
$obRouter->get("/home",[
    "middlewares" => [
        "required-login",
    ],
    function(){
        return new Response(200, Pages\Home::render());
    }
]);




//ROTA PARA VIEW DE NOTÍCIAS
$obRouter->get("/news",  [
    "middlewares" => [
        "required-login",
    ],
    function (){
    return new Response(200, Pages\News::render());
}]);




//ROTA PARA PREVIEW DE NOTÍCIA
$obRouter->get("/news/preview/{id}",  [
    "middlewares" => [
        "required-login",
    ],
    function () {
        return new Response(200, Pages\Preview::render());
    }
]);





//ROTA PARA VIEW DE USUÁRIOS
$obRouter->get("/users",[
    "middlewares" => [
        "required-login",
        "required-administrator"
    ],
    function(){
        return new Response(200, Pages\Users::render());
    }
]);




//ROTA PARA VIEW DE PERFIL DE USUÁRIO
$obRouter->get("/profile",[
    "middlewares" => [
        "required-login",
    ],
    function(){
        return new Response(200, Pages\Profile::render());
    }
]);




//ROTA PARA LOGOUT DE USUÁRIO
$obRouter->get("/logout",[
    "middlewares" => [
        "required-login",
    ],
    function($request){
        return new Response(200, Pages\Logout::render($request));
    }
]);




//ROTA PARA PÁGINA NÃO ENCONTRADA
$obRouter->get("/not-found",  [
    function () {
        return new Response(404, Pages\NotFound::render());
    }
]);














