<?php

namespace App\Controller\Api;

use App\Service\UserService;
use App\Repository\UserRepository;

use \App\Core\Request;
use App\Database\Database;

use \Exception;


class UserApi
{


    /**
     * Instância de UserService
     * @var UserService|null
     */
    private static ?UserService $userService = null;



    /**
     * Método responsável por inicializar o UserService estaticamente
     * 
     * @return void
     */
    public static function initialize(): void
    {
        if (self::$userService === null) {
            $db = new Database("user");
            $userRepository = new UserRepository($db);
            self::$userService = new UserService($userRepository);
        }
    }




    /**
     * Método responsável por processar o login de um usuário
     * @param Request $request
     * @return string
     */
    public static function login(Request $request): string
    {
        try {

            self::initialize();

            $userData = $request->getPostVars();

            self::$userService->login($userData);

            return json_encode([
                "success" => true,
                "message" => "Usuário autenticado!",
            ]);
        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }



    /**
     * Método responsável por processar o cadastro de um usuário
     *
     * @param Request $request
     * @return string
     */
    public static function register(Request $request): string
    {
        try {

            self::initialize();

            $userData = $request->getPostVars();

            $randomPassword = self::$userService->register($userData);


            return json_encode([
                "success" => true,
                "message" => "Usuário cadastrado com sucesso!",
                "data" => ["password" => $randomPassword]
            ]);
        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }

}
