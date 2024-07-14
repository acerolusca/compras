<?php

namespace App\Controller\Api;

use App\Service\GraduateService;
use App\Repository\GraduateRepository;

use App\Model\Graduate;

use App\Core\Database;
use \DateTime;
use \Exception;
use \App\Http\Request;

class GraduateApi
{


    /**
     * Instância de Administrator Service
     *
     * @var GraduateService|null
     */
    private static ?GraduateService $graduateService = null;



    /**
     * Método responsável por inicializar o GraduateService estaticamente
     * 
     * @return void
     */
    public static function initialize(): void
    {
        if (self::$graduateService === null) {
            $db = new Database("graduate");
            $graduateRepository = new GraduateRepository($db);
            self::$graduateService = new GraduateService($graduateRepository);
        }
    }



    /**
     * Método responsável por logar um egresso no formulário
     *
     * @param Request $request
     * @return string
     */
    public static function login(Request $request): string {

        try {

            self::initialize();

            $formFields = $request->getPostVars();

            
            $cpf = $formFields["cpf"] ?? "";
            $birthDate = $formFields["birthDate"] ?? "";


            self::$graduateService->login($cpf, $birthDate);

            return json_encode([
                "success" => true,
                "message" => "Egresso autenticado!",
            ]);
        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
        

    }



    /**
     * Método responsável por deslogar um egresso no formulário
     *
     * @return string
     */
    public static function logout(): string{
        try{
            $graduate = new Graduate();
            $graduate->logout();
            return json_encode([
                "success" => true,
                "message" => "Egresso deslogado!",
            ]);

        } catch(Exception $e){
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }




    /**
     * Método responsável por cadastrar um egresso
     *
     * @param Request $request
     * @return string
     */
    public static function register(Request $request): string
    {
        try {

            self::initialize();

            $formFields = $request->getPostVars();

            $graduateData = [
                "cpf" => $formFields["registerCpf"] ?? "",
                "birthDate" => $formFields["registerBirthDate"] ?? "",
            ];


            self::$graduateService->register($graduateData);


            return json_encode([
                "success" => true,
                "message" => "Egresso cadastrado com sucesso!"
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }



    /**
     * Método responsável por editar um egresso
     *
     * @param Request $request
     * @return string
     */
    public static function edit(Request $request): string
    {
        try {

            self::initialize();

            $formFields = $request->getPostVars();
;

            $graduateData = [
                "lastCpf" =>  $formFields["editLastCpf"] ?? "",
                "cpf" => $formFields["editCpf"] ?? "",
                "birthDate" => $formFields["editBirthDate"] ?? "",
            ];


            self::$graduateService->edit($graduateData);


            return json_encode([
                "success" => true,
                "message" => "Egresso editado com sucesso!"
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }




    /**
     * Método responsável por editar um egresso
     *
     * @param string $cpf
     * @return string
     */
    public static function delete(string $cpf): string
    {
        try {

            self::initialize();


            self::$graduateService->delete($cpf);


            return json_encode([
                "success" => true,
                "message" => "Egresso excluído com sucesso!"
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }




    /**
     * Método responsável por retornar as informações de um egresso pelo cpf
     *
     * @param string $cpf
     * @return string
     */
    public static function getByCpf(string $cpf): string {

        try {

            self::initialize();

            $data = self::$graduateService->getByCpf($cpf);


            return json_encode([
                "success" => true,
                "data" => $data,
                "message" => "Egresso encontrado"
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }


    }

}
