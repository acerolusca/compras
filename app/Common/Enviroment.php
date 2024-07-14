<?php

namespace App\Common;

class Enviroment {

    /**
     * Método reponsável por adicionar variáveis de ambiente
     *
     * @param string $dir
     * @return false|void
     */
    public static function load($dir){
        
        //VERIFICA SE O ARQUIVO .ENV EXISTE
        if(!file_exists($dir)){
            return false;
        }

        //RECUPERA O ARQUIVO DE VARIÁVES COMO UM ARRRAY DE LINHAS
        $lines = file($dir . ".env");


        //DEFINE AS VARIÁVEIS DE AMBIENTE
        foreach($lines as $line){
            putenv(trim($line));
        }

    }
}