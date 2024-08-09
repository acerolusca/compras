<?php

use \App\Controller\Api;
use \App\Core\Response;


/**
 * @var \App\Core\Router $obRouter
 */



//---------------------------------------- Usuários ------------------------------------------//



//ROTA PARA LOGIN DE USUÁRIO
$obRouter->post("/login",[
    "middlewares" => [
        "requireLogout",
    ],
    function($request){
        return new Response(200, Api\UserApi::login($request), "application/json");
    }
]);



//ROTA PARA CADASTRO DE USUÁRIO
$obRouter->post("/user/register",[
    "middlewares" => [
        "requireLogin",
        "requireAdministrator"
    ],
    function($request){
        return new Response(200, Api\UserApi::register($request), "application/json");
    }
]);



//ROTA PARA EDITAR AS INFORMAÇÕES DE UM USUÁRIO
$obRouter->post("/user/edit",[
    "middlewares" => [
        "requireLogin",
        "requireAdministrator"
    ],
    function($request){
        return new Response(200, Api\UserApi::edit($request), "application/json");
    }
]);




//ROTA PARA REDEFINIR A SENHA DE UM USUÁRIO NO PRIMEIRO ACESSO
$obRouter->post("/user/redefine-first-access-password",[
    "middlewares" => [
        "requireLogin",
    ],
    function($request){
        return new Response(200, Api\UserApi::redefineFirstAccessPassword($request), "application/json");
    }
]);



//ROTA PARA DELETAR AS INFORMAÇÕES DE UM USUÁRIO
$obRouter->post("/user/delete/{cpf}",[
    "middlewares" => [
        "requireLogin",
        "requireAdministrator"
    ],
    function($cpf){
        return new Response(200, Api\UserApi::delete($cpf), "application/json");
    }
]);



//ROTA PARA GERAR UMA NOVA SENHA PARA UM USUÁRIO
$obRouter->post("/user/generate-new-password/{cpf}",[
    "middlewares" => [
        "requireLogin",
        "requireAdministrator"
    ],
    function($cpf){
        return new Response(200, Api\UserApi::generateNewPassword($cpf), "application/json");
    }
]);



//ROTA PARA EDITAR AS INFORMAÇÕES DE PERFIL DE UM USUÁRIO
$obRouter->post("/user/profile/edit",[
    "middlewares" => [
        "requireLogin",
    ],
    function($request){
        return new Response(200, Api\UserApi::editProfile($request), "application/json");
    }
]);



//ROTA PARA EDITAR SENHA DO ADMINISTRADOR
$obRouter->post("/user/profile/edit-password",[
    "middlewares" => [
        "requireLogin",
    ],
    function($request){
        return new Response(200, Api\UserApi::editPassword($request), "application/json");
    }
]);



//ROTA PARA BUSCAR AS INFORMAÇÕES DE UM USUÁRIO PELO CPF
$obRouter->get("/user/{cpf}",[
    "middlewares" => [
        "requireLogin",
        "requireAdministrator"
    ],
    function($cpf){
        return new Response(200, Api\UserApi::getInfo($cpf), "application/json");
    }
]);






//---------------------------------------- Notícias ------------------------------------------//

//ROTA PARA BUSCAR AS INFORMAÇÕES DE TODAS AS NOTÍCIAS
$obRouter->get("/news/all",[
    "middlewares" => [
        "requireLogin",
    ],
    function(){
        return new Response(200, Api\NewsApi::getAll(), "application/json");
    }
]);



//ROTA PARA CADASTRO DE NOTÍCIA
$obRouter->post("/news/register",[
    "middlewares" => [
        "requireLogin"
    ],
    function($request){
        return new Response(200, Api\NewsApi::register($request), "application/json");
    }
]);





//ROTA PARA EDITAR AS INFORMAÇÕES DE UMA NOTÍCIA
$obRouter->post("/news/edit",[
    "middlewares" => [
        "requireLogin"
    ],
    function($request){
        return new Response(200, Api\NewsApi::edit($request), "application/json");
    }
]);



//ROTA PARA DELETAR AS INFORMAÇÕES DE UMA NOTÍCIA
$obRouter->post("/news/delete/{id}",[
    "middlewares" => [
        "requireLogin"
    ],
    function($id){
        return new Response(200, Api\NewsApi::delete($id), "application/json");
    }
]);




//ROTA PARA MUDAR O STATUS DE DESTAQUE DE UMA NOTÍCIA NOTÍCIA
$obRouter->post("/news/change-highlighted",[
    "middlewares" => [
        "requireLogin"
    ],
    function($request){
        return new Response(200, Api\NewsApi::changeHighlighted($request), "application/json");
    }
]);




//ROTA PARA MUDAR O STATUS DE VISIBILIDADE DE UMA NOTÍCIA NOTÍCIA
$obRouter->post("/news/change-visible",[
    "middlewares" => [
        "requireLogin"
    ],
    function($request){
        return new Response(200, Api\NewsApi::changeVisible($request), "application/json");
    }
]);






//ROTA PARA BUSCAR AS INFORMAÇÕES BÁSICAS DE TODAS AS NOTÍCIAS DISPONÍVEIS
$obRouter->get("/news/available/all",[
    function(){
        return new Response(200, Api\NewsApi::getAllAvailable(), "application/json");
    }
]);





//ROTA PARA BUSCAR AS INFORMAÇÕES BÁSICAS DAS NOTÍCIAS DESTAQUE
$obRouter->get("/news/available/highlighted",[
    function(){
        return new Response(200, Api\NewsApi::getHighlightedAvailable(), "application/json");
    }
]);





//ROTA PARA BUSCAR AS INFORMAÇÕES BÁSICAS DAS NOTÍCIAS DESTAQUE
$obRouter->get("/news/available/regular",[
    function(){
        return new Response(200, Api\NewsApi::getRegularAvailable(), "application/json");
    }
]);





//ROTA PARA BUSCAR NOTÍCIAS DE ACORDO COM ALGUM PARÂMETRO
$obRouter->get("/news/available/search",[
    function($request){
        return new Response(200, Api\NewsApi::search($request), "application/json");
    }
]);





//ROTA PARA BUSCAR AS INFORMAÇÕES DE UMA NOTÍCIA DISPONÍVEL PELO ID
$obRouter->get("/news/available/{id}",[
    function($id){
        return new Response(200, Api\NewsApi::getInfoAvailable($id), "application/json");
    }
]);





//ROTA PARA BUSCAR AS INFORMAÇÕES DE UMA NOTÍCIA PELO ID
$obRouter->get("/news/{id}",[
    "middlewares" => [
        "requireLogin"
    ],
    function($id){
        return new Response(200, Api\NewsApi::getInfo($id), "application/json");
    }
]);






//ROTA PARA BUSCAR AS INFORMAÇÕES DE UMA NOTÍCIA PELO ID
$obRouter->post("/editor/uploader",[
    "middlewares" => [
        "requireLogin"
    ],
    function(){
        return new Response(200, Api\NewsApi::uploadEditorImage(), "application/json");
    }
]);





























