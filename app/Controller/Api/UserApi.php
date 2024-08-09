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

            $userRegisterData = $request->getPostVars();

            $randomPassword = self::$userService->register($userRegisterData);


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




    /**
     * Método responsável por processar a busca de um usuário a partir de seu CPF
     *
     * @param string $cpf
     * @return string
     */
    public static function getInfo(string $cpf): string {

        try {

            self::initialize();

            $data = self::$userService->getInfo($cpf);


            return json_encode([
                "success" => true,
                "data" => $data,
                "message" => "Usuário encontrado!"
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }

    }




    /**
     * Método responsável por processar a edição de um usuário quando realizada pro um administrador
     *
     * @param Request $request
     * @return string
     */
    public static function edit(Request $request): string
    {
        try {

            self::initialize();

            $userEditData = $request->getPostVars();


            self::$userService->edit($userEditData);


            return json_encode([
                "success" => true,
                "message" => "Usuário editado com sucesso!"
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }




    /**
     * Método reponsável por processar a geração de uma nova senha para o usuário
     * @param string $cpf
     * @return string
     */
    public static function generateNewPassword($cpf): string {

        try {

            self::initialize();

            $newPassword = self::$userService->generateNewPassword($cpf);


            return json_encode([
                "success" => true,
                "message" => "Nova senha gerada com sucesso!",
                "password" => $newPassword
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }




    /**
     * Método responsável por deletar um usuário
     *
     * @param string $cpf
     * @return string
     */
    public static function delete(string $cpf): string
    {
        try {

            self::initialize();


            self::$userService->delete($cpf);


            return json_encode([
                "success" => true,
                "message" => "Usuário excluído com sucesso!"
            ]);


        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }


    

    /**
     * 
     * Método responsável por processar a edição das informações de perfil de um usuário
     * @param Request $request
     * @return string
     */
    public static function editProfile(Request $request): string {
        try{

            self::initialize();

            $userProfileData = $request->getPostVars();
            $userProfileData["imageTmpName"] = $_FILES["photoInput"]["tmp_name"] ?? "";


            $stayLoggedIn = self::$userService->editProfile($userProfileData);


            return json_encode([
                "success" => true,
                "message" => "Informações alteradas com sucesso!",
                "stayLoggedIn" => $stayLoggedIn
            ]);


        } catch (Exception $e){
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }

    }



    /**
     * Método responsável por editar senha do usuário
     *
     * @param Request $request
     * @return string
     */
    public static function editPassword(Request $request): string {

        try {

            self::initialize();

            $editPasswordData = $request->getPostVars();

            self::$userService->editPassword($editPasswordData);

            return json_encode([
                "success" => true,
                "message" => "Senha alterada com sucesso!",
            ]);

        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }




    /**
     * Método responsável por redefinir a senha do usuário no primeiro acesso
     *
     * @param Request $request
     * @return string
     */
    public static function redefineFirstAccessPassword(Request $request) {

        try{

            self::initialize();

            $redefineFirstAccessPasswordData = $request->getPostVars();

            self::$userService->redefineFirstAccessPassword($redefineFirstAccessPasswordData);

            return json_encode([
                "success" => true,
                "message" => "Senha redefinida com sucesso!",
            ]);

        } catch(Exception $e){
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);

        }

    }

}
