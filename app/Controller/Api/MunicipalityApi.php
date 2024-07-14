<?php

namespace App\Controller\Api;

use App\Service\MunicipalityService;
use App\Repository\MunicipalityRepository;

use App\Core\Database;
use \Exception;





class MunicipalityApi
{


    
    /**
     * Instância de MunicipalityService
     *
     * @var MunicipalityService|null
     */
    private static ?MunicipalityService $municipalityService = null;



    /**
     * Método responsável por inicializar o MunicipalityService estaticamente
     * 
     * @return void
     */
    public static function initialize(): void
    {
        if (self::$municipalityService === null) {
            $db = new Database("municipality");
            $municipalityRepository = new MunicipalityRepository($db);
            self::$municipalityService = new MunicipalityService($municipalityRepository);
        }
    }


    public static function getByState($state){

        try {

            self::initialize();


            $data = self::$municipalityService->getByState($state);


            return json_encode([
                "success" => true,
                "data" => $data
            ]);


        } catch (Exception $e){
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }

    }



}
