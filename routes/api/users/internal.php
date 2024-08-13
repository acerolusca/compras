<?php

use \App\Controller\Api\UserApi;
use \App\Core\Response;


/**
 * @var \App\Core\Router $obRouter
 */



//ROTA PARA LOGIN DE USUÁRIO
$obRouter->post("/user/login",[
    "middlewares" => [
        "required-logout",
    ],
    function($request){
        return new Response(200, UserApi::login($request), "application/json");
    }
]);



//ROTA PARA CADASTRO DE USUÁRIO
$obRouter->post("/user/register",[
    "middlewares" => [
        "required-login",
        "required-administrator"
    ],
    function($request){
        return new Response(200, UserApi::register($request), "application/json");
    }
]);




//ROTA PARA BUSCAR AS INFORMAÇÕES DE UM USUÁRIO PELO CPF
$obRouter->get("/user/info/{cpf}",[
    "middlewares" => [
        "required-login",
        "required-administrator"
    ],
    function($cpf){
        return new Response(200, UserApi::getInfo($cpf), "application/json");
    }
]);




//ROTA PARA EDIÇÃO DE USUÁRIO
$obRouter->post("/user/edit",[
    "middlewares" => [
        "required-login",
        "required-administrator"
    ],
    function($request){
        return new Response(200, UserApi::edit($request), "application/json");
    }
]);




//ROTA PARA REDEFINIR A SENHA DE UM USUÁRIO NO PRIMEIRO ACESSO
$obRouter->post("/user/redefine-first-access-password",[
    "middlewares" => [
        "required-login",
    ],
    function($request){
        return new Response(200, UserApi::redefineFirstAccessPassword($request), "application/json");
    }
]);



//ROTA PARA DELETAR AS INFORMAÇÕES DE UM USUÁRIO PELO CPF
$obRouter->post("/user/delete/{cpf}",[
    "middlewares" => [
        "required-login",
        "required-administrator"
    ],
    function($cpf){
        return new Response(200, UserApi::delete($cpf), "application/json");
    }
]);



//ROTA PARA GERAR UMA NOVA SENHA PARA UM USUÁRIO
$obRouter->post("/user/generate-new-password/{cpf}",[
    "middlewares" => [
        "required-login",
        "required-administrator"
    ],
    function($cpf){
        return new Response(200, UserApi::generateNewPassword($cpf), "application/json");
    }
]);



//ROTA PARA EDITAR AS INFORMAÇÕES DE PERFIL DE UM USUÁRIO
$obRouter->post("/user/profile/edit",[
    "middlewares" => [
        "required-login",
    ],
    function($request){
        return new Response(200, UserApi::editProfile($request), "application/json");
    }
]);



//ROTA PARA EDITAR SENHA DO USUÁRIO
$obRouter->post("/user/profile/edit-password",[
    "middlewares" => [
        "required-login",
    ],
    function($request){
        return new Response(200, UserApi::editPassword($request), "application/json");
    }
]);






































