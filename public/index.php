<?php

require __DIR__ . "/../includes/app.php";

use \App\Core\Router;
use \App\Core\Middleware\Queue;


//INICIA O ROUTER
$obRouter = new Router(getenv("URL"));



//INCLUI AS ROTAS PARA API's 
include __DIR__ . "/../routes/api/index.php";


//INCLUI AS ROTAS PARA IMAGENS
include __DIR__ . "/../routes/images/index.php";


//INCLUI AS ROTAS DE PÁGINAS
include __DIR__ . "/../routes/pages/index.php";




// DEFINE O MAPEAMENTO DE MIDDLEWARES
Queue::setMap([
    "required-logout" => \App\Core\Middleware\RequiredLogout::class,
    "required-login" => \App\Core\Middleware\RequiredLogin::class,
    "required-administrator" => \App\Core\Middleware\RequiredAdministrator::class,
    "required-website-origin" => \App\Core\Middleware\RequiredWebsiteOrigin::class,
    "required-authorization" => \App\Core\Middleware\RequiredAuthorization::class
]);



// DEFINE O MAPEAMENTO DE MIDDLEWARES PADRÕES (EXECUTADOS EM TODAS AS ROTAS)
Queue::setDefault([]);



//IMPRIME O RESPONSE DA ROTA
$obRouter->run()->sendResponse();












