<?php

namespace App\Controller\Api;

use App\Service\FormEditionService;
use App\Repository\FormEditionRepository;

use App\Repository\FormDataRepository;

use App\Core\Database;
use \DateTime;
use \Exception;
use \App\Http\Request;

class FormEditionApi
{


    /**
     * Instância de FormEditionService
     *
     * @var FormEditionService|null
     */
    private static ?FormEditionService $formEditionService = null;




    /**
     * Método responsável por inicializar o FormEditionService estaticamente
     * 
     * @return void
     */
    public static function initialize(): void
    {
        if (self::$formEditionService === null) {
            $formEditionDb = new Database("form_edition");
            $formEditionRepository = new FormEditionRepository($formEditionDb);

            $formDataDb = new Database("form_data");
            $formDataRepository = new FormDataRepository($formDataDb);


            self::$formEditionService = new FormEditionService($formEditionRepository, $formDataRepository);
        }
    }



    /**
     * Método responsável por cadastar uma edição de formulário na plataforma
     *
     * @param Request $request
     * @return string
     */
    public static function register(Request $request): string
    {
        try {

            self::initialize();

            $formFields = $request->getPostVars();

            $formEditionData = [
                "startDate" => $formFields["registerStartDate"] ?? "",
                "endDate" => $formFields["registerEndDate"] ?? ""
            ];


            self::$formEditionService->register($formEditionData);


            return json_encode([
                "success" => true,
                "message" => "Formulário cadastrado com sucesso!",
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }


    
    /**
     * Método responsável por alterar o período de vigência do formulário
     *
     * @param Request $request
     * @return string
     */
    public static function changeValidityPeriod(Request $request): string
    {
        try {

            self::initialize();

            $formFields = $request->getPostVars();


            $formEditionData = [
                "startDate" => $formFields["changeValidityPeriodStartDate"] ?? "",
                "endDate" => $formFields["changeValidityPeriodEndDate"] ?? ""
            ];


            self::$formEditionService->changeValidityPeriod($formEditionData);


            return json_encode([
                "success" => true,
                "message" => "Período de vigência alterado com sucesso!",
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }



     /**
     * Método responsável por alterar o período de vigência do formulário
     *
     * @return string
     */
    public static function delete(): string
    {
        try {

            self::initialize();


            self::$formEditionService->delete();


            return json_encode([
                "success" => true,
                "message" => "Edição de formulário excluída com sucesso!",
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }




    /**
     * Método responsável por alterar o prazo final do formulário
     *
     * @param Request $request
     * @return string
     */
    public static function editEndDate(Request $request): string
    {
        try {

            self::initialize();

            $formFields = $request->getPostVars();



            $formEditionData = [
                "endDate" => $formFields["editEndDate"] ?? ""
            ];


            self::$formEditionService->editEndDate($formEditionData);


            return json_encode([
                "success" => true,
                "message" => "Prazo final alterado com sucesso!",
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }




    /**
     * Método responsável por bloquear o formulário temporariamente 
     *
     * @return string
     */
    public static function blockTemporarily(): string
    {
        try {

            self::initialize();


            self::$formEditionService->blockTemporarily();


            return json_encode([
                "success" => true,
                "message" => "Edição de formulário bloqueada com sucesso!",
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }




    /**
     * Método responsável por desbloquaer o formulário
     *
     * @return string
     */
    public static function unlock(): string
    {
        try {

            self::initialize();

            self::$formEditionService->unlock();


            return json_encode([
                "success" => true,
                "message" => "Formulário desbloqueado com sucesso!",
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }





    /**
     * Método responsável por encerrar o formulário
     *
     * @return string
     */
    public static function close(): string
    {
        try {

            self::initialize();

            self::$formEditionService->close();


            return json_encode([
                "success" => true,
                "message" => "Formulário encerrado com sucesso!",
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }



    /**
     * Método responsável por retornar os dados da última edição de formulário
     *
     * @return string
     */
    public static function getLatestFormEdition(): string {

        try {

            self::initialize();

            $latestFormEdition = self::$formEditionService->getLatestFormEdition();

            if(count($latestFormEdition) == 0) {
                throw new Exception("Edição de formulário não encontrada!", 400);
            }

            return json_encode([
                "success" => true,
                "data" => $latestFormEdition,
                "message" => "Última edição encontrada!",
            ]);



        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }

    }




}
