<?php


use \App\Controller\Pages;
use \App\Core\Response;



//ROTA PARA LOGIN DE USUÁRIO
$obRouter->get("/",[
    "middlewares" => [
        "requireLogout",
    ],
    function(){
        return new Response(200, Pages\Login::render());
    }
]);



//ROTA PARA LOGIN DE USUÁRIO
$obRouter->get("/users",[
    "middlewares" => [
        "requireLogin",
        "requireAdministrator"
    ],
    function(){
        return new Response(200, Pages\Users::render());
    }
]);













