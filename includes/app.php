<?php

//COMPOSER - AUTOLOAD
require __DIR__ . "/../vendor/autoload.php";

use \App\Utils\View;
use \App\Common\Enviroment;
use \App\Database\Database;


//CARREGA AS VARIAVEIS DE AMBIENTE DO PROJETO
Enviroment::load(__DIR__ ."/../");


//DEFINE AS CONFIGURAÇÕES DO BANCO DE DADOS
Database::config(
    getenv("DB_HOST"), 
    getenv("DB_NAME"), 
    getenv("DB_USER"),
    getenv("DB_PASS"),
    getenv("DB_PORT")
);


//DEFINE A CONSTANTE DE URL
define("URL",getenv("URL"));


//DEFINE AS CONSTANTES DAS PÁGINAS
View::init([
    "URL" => URL
]);

