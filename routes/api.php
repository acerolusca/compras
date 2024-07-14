<?php


use \App\Controller\Api;
use \App\Core\Response;



//ROTA PARA LOGIN DE USUÁRIO
$obRouter->POST("/login",[
    "middlewares" => [
        "requireLogout",
    ],
    function($request){
        return new Response(200, Api\UserApi::login($request), "application/json");
    }
]);



//ROTA PARA CADASTRO DE USUÁRIO
$obRouter->POST("/user/register",[
    "middlewares" => [
        "requireLogin",
        "requireAdministrator"
    ],
    function($request){
        return new Response(200, Api\UserApi::register($request), "application/json");
    }
]);






























