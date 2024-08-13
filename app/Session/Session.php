<?php

namespace App\Session;


use Exception;

abstract class Session {


    protected string $sessionName = "";

    /**
     * Método responsável por iniciar uma sessão não ativa 
     * @return void
     */
    public function init() {

        //VERIFICA SE A SESSÃO NÃO ESTÁ ATIVA
        if(session_status() !== PHP_SESSION_ACTIVE){
            //INICIA  SESSÃO
           $this->start();
        } else {
          $this->sessionName = sha1("compras_ma" . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
        }
    }


    /**
     * Método responsável por iniciar a sessão
     * @return void
     */
    private function start() {

        //DEFINE E ATRIBUI O NOME DA SESSÃO
        $sessionName = sha1("compras_ma" . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
        session_name($sessionName);
        $this->sessionName = $sessionName;

        //INICIA A SESSÃO
        session_start();
    }



    /**
     * Método abstrato de logout implementado pelas classes filhas
     *
     * @return void
     */
    abstract public function logout();


}