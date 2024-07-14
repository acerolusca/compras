<?php

namespace App\Controller\Api;

use App\Service\AdministratorService;
use App\Repository\AdministratorRepository;


use App\Core\Database;
use App\Session\GraduateSession;
use \DateTime;
use \Exception;
use \App\Http\Request;

class TesteApi
{


    public static function test(): string
    {
        try {

            (new GraduateSession())->init();
            
            echo json_encode($_SESSION);

            return json_encode($_SESSION);
        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }


}
