<?php

namespace App\Controller\Pages;

use App\Model\User;



class Logout {
    /**
     * Método responsável por deslogar o usuário e redirecioná-lo para o login
     * @return void
     */
    public static function render($request) {
        $user = new User();
        $user->logout();
        $request->getRouter()->redirect("/");
    }

}