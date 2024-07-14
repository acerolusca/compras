<?php

namespace App\Session;


use Exception;

class UserSession extends Session {

    /**
     * Implementação do método logout para usuário
     *
     * @return void
     */
    public function logout() {
        $this->init();
        if(isset($_SESSION["user"])){
            unset($_SESSION["user"]);
        }
        setcookie($this->sessionName, "", time() - 3600, "/");
    }

}