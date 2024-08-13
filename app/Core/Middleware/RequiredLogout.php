<?php
namespace App\Core\Middleware;


use \Exception;
use \App\Model\User;

class RequiredLogout {

    public function handle($request, $next) {
        
        $user = new User();

        try {

            if ($user->isLogged()) {
                $request->getRouter()->redirect("/home");
            }

            return $next($request);

        } catch (Exception $e) { 
            $user->logout();
            $request->getRouter()->redirect("/");
        }

    }
}