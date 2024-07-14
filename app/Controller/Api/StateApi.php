<?php

namespace App\Controller\Api;

use App\Service\StateService;
use App\Repository\StateRepository;

use App\Core\Database;
use \Exception;




class StateApi
{


    /**
     * Instância de StateService
     *
     * @var StateService|null
     */
    private static ?StateService $stateService = null;



    /**
     * Método responsável por inicializar o StateService estaticamente
     * 
     * @return void
     */
    public static function initialize(): void
    {
        if (self::$stateService === null) {
            $db = new Database("state");
            $stateRepository = new StateRepository($db);
            self::$stateService = new StateService($stateRepository);
        }
    }


    public static function getAll()
    {

        try {

            self::initialize();


            $data = self::$stateService->getAll();

            return json_encode([
                "success" => true,
                "data" => $data
            ]);
        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }
}
