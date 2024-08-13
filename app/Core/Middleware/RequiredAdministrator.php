<?php
namespace App\Core\Middleware;

use \App\Model\User;
use \Exception;


class RequiredAdministrator {
    
    public function handle($request, $next) {

        $user = new User();

        try {
            if ($user->getSessionPrivilege() != "administrator") {
                $user->logout();
                $request->getRouter()->redirect("/");
            }

            return $next($request);

        } catch (Exception $e) {
            $user->logout();
            $request->getRouter()->redirect("/");
        }

    }
}