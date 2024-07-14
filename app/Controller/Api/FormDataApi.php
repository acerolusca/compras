<?php

namespace App\Controller\Api;



use App\Service\FormDataService;
use App\Model\Graduate;
use App\Repository\FormDataRepository;
use App\Service\FormEditionService;
use App\Repository\FormEditionRepository;

use App\Core\Database;
use \Exception;
use \App\Http\Request;

use \App\Utils\View;



class FormDataApi {


    /**
     * Instância de FormDataService
     *
     * @var FormDataService|null
     */
    private static ?FormDataService $formDataService = null;



    /**
     * Instância de FormEditonService
     *
     * @var FormEditionService|null
     */
    private static ?FormEditionService $formEditionService = null;




    /**
     * Método responsável por inicializar o AdministratorService estaticamente
     * 
     * @return void
     */
    public static function initialize(): void
    {
        if (self::$formDataService === null || self::$formEditionService === null) {
            $formDataDb = new Database("form_data");
            $formDataRepository = new FormDataRepository($formDataDb);
            self::$formDataService = new FormDataService($formDataRepository);

            $formEditionDb = new Database("form_edition");
            $formEditionRepository = new FormEditionRepository($formEditionDb);
            self::$formEditionService = new FormEditionService($formEditionRepository, $formDataRepository);
        }
    }


    /**
     * Método responsável por registrar as informações do formulário do egresso
     *
     * @param Request $resquest
     * @return string
     */
    public static function register(Request $request): string {

        try {

            self::initialize();


            $latestFormEditionStatus = self::$formEditionService->refreshFormEditionStatus();

            if($latestFormEditionStatus != "Ativo") {
                throw new Exception(
                    "Este formulário não está aceitando respostas no momento. Por favor, aguarde o período de submissões."
                );
            } 

            $formFields = $request->getPostVars();



            $formDataArray = [
                "cpf" => $formFields["cpf"] ?? "",
                "fullName" => $formFields["fullName"] ?? "",
                "gender" => $formFields["gender"],
                "liveInBrazil" => $formFields["liveInBrazil"] ?? "",
                "country" => $formFields["country"] ?? "",
                "state" => $formFields["state"] ?? "",
                "municipality" => $formFields["municipality"] ?? "",
                "undergraduateDegree" => $formFields["undergraduateDegree"] ?? "",
                "undergraduateCompletionYear" => $formFields["undergraduateCompletionYear"] ?? "",
                "researchArea" => $formFields["researchArea"] ?? "",
                "postgraduateStartYear" => $formFields["postgraduateStartYear"] ?? "",
                "postgraduateCompletionYear" => $formFields["postgraduateCompletionYear"] ?? "",
                "currentlyWorking" => $formFields["currentlyWorking"] ?? "",
                "workingInstitution" => $formFields["workingInstitution"] ?? "",
                "workingSector" => $formFields["workingSector"] ?? "",
                "currentSalary" => $formFields["currentSalary"] ?? "",
                "teachingField" => $formFields["teachingField"] ?? "",
                "additionalPostgraduate" => $formFields["additionalPostgraduate"] ?? "",
                "publishData" => $formFields["publishData"] ?? "",
                "formEdition" => $formFields["formEdition"] ?? ""
             ];



            self::$formDataService->register($formDataArray);


            return json_encode([
                "success" => true,
                "message" => "Formulário enviado com sucesso!",
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }

    }



    /**
     * Método responsável por editar uma submissão de um egresso específico em uma edição específica
     *
     * @param Request $resquest
     * @return string
     */
    public static function edit(Request $request): string {

        try {

            self::initialize();


            $formFields = $request->getPostVars();


            $formDataArray = [
                "cpf" => $formFields["cpf"] ?? "",
                "fullName" => $formFields["fullName"] ?? "",
                "gender" => $formFields["gender"],
                "liveInBrazil" => $formFields["liveInBrazil"] ?? "",
                "country" => $formFields["country"] ?? "",
                "state" => $formFields["state"] ?? "",
                "municipality" => $formFields["municipality"] ?? "",
                "undergraduateDegree" => $formFields["undergraduateDegree"] ?? "",
                "undergraduateCompletionYear" => $formFields["undergraduateCompletionYear"] ?? "",
                "researchArea" => $formFields["researchArea"] ?? "",
                "postgraduateStartYear" => $formFields["postgraduateStartYear"] ?? "",
                "postgraduateCompletionYear" => $formFields["postgraduateCompletionYear"] ?? "",
                "currentlyWorking" => $formFields["currentlyWorking"] ?? "",
                "workingInstitution" => $formFields["workingInstitution"] ?? "",
                "workingSector" => $formFields["workingSector"] ?? "",
                "currentSalary" => $formFields["currentSalary"] ?? "",
                "teachingField" => $formFields["teachingField"] ?? "",
                "additionalPostgraduate" => $formFields["additionalPostgraduate"] ?? "",
                "publishData" => $formFields["publishData"] ?? "",
                "formEdition" => $formFields["formEdition"] ?? ""
             ];



            self::$formDataService->edit($formDataArray);


            return json_encode([
                "success" => true,
                "message" => "Submissão alterada com sucesso!",
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }

    }




    /**
     * Método responsável por retornar uma submissão de um egresso específico em uma edição específica
     *
     * @param string $cpf
     * @param string $formEdition
     * @return string
     */
    public static function getSpecificSubmission(string $cpf, string $formEdition): string {

        try{
            self::initialize();

            $submission = self::$formDataService->getSpecificSubmission($cpf, $formEdition);

            if(count($submission) == 0) {
                throw new Exception("Submissão não encontrada!", 400);
            }

            return json_encode([
                "success" => true,
                "data" => $submission,
                "message" => "Submissão encontrada!",
            ]);


        } catch(Exception $e){
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);

        }

    }












}