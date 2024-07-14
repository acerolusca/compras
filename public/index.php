<?php

require __DIR__ . "/../includes/app.php";

use \App\Core\Router;
use \App\Core\Middleware\Queue;


//INICIA O ROUTER
$obRouter = new Router(URL);

//INCLUI AS ROTAS DE PÁGINAS
include __DIR__ . "/../routes/pages.php";


//INCLUI AS ROTAS DE API's
include __DIR__ . "/../routes/api.php";


//INCLUI AS ROTAS PARA IMAGENS
include __DIR__ . "/../routes/images.php";



// MIDDLEWARE MAPPING
Queue::setMap([
    "requireLogout" => \App\Core\Middleware\RequireLogout::class,
    "requireLogin" => \App\Core\Middleware\RequireLogin::class,
    "requireAdministrator" => \App\Core\Middleware\RequireAdministrator::class
]);


Queue::setDefault([]);

//IMPRIME O RESPONSE DA ROTA
$obRouter->run()->sendResponse();












