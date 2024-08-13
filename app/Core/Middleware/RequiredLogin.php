<?php
namespace App\Core\Middleware;

use \App\Model\User;
use Exception;



class RequiredLogin {
    public function handle($request, $next) {

        $user = new User();
        
        try {
            if (!$user->isLogged()) {
                $request->getRouter()->redirect("/");
            }
            return $next($request);

        } catch (Exception $e) {
            $user->logout();
            $request->getRouter()->redirect("/");
        }
    }
}