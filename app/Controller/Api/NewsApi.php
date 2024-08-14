<?php

namespace App\Controller\Api;


use App\Service\NewsService;
use App\Repository\NewsRepository;
use App\Core\Request;
use App\Database\Database;


use \Exception;


class NewsApi
{


    /**
     * Instância de NewsService
     * @var NewsService|null
     */
    private static ?NewsService $newsService = null;



    /**
     * Método responsável por inicializar o NewsService estaticamente
     * 
     * @return void
     */
    public static function initialize(): void
    {
        if (self::$newsService === null) {
            $db = new Database("news");
            $newsRepository = new NewsRepository($db);
            self::$newsService = new NewsService($newsRepository);
        }
    }



    /**
     * Método responsável por retornar as informações de todas as notícias cadastradas no sistema
     * @return string
     */
    public static function getAll(): string
    {
        try {

            self::initialize();

            $data = self::$newsService->getAll();

            $message = count($data) == 0 ? "Ainda não há notícias cadastradas" : "";

            return json_encode([
                "success" => true,
                "data" =>  $data,
                "message" => $message
            ]);
        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }



    /**
     * Método responsável por processar o cadastro de uma notícia
     *
     * @param Request $request
     * @return string
     */
    public static function register(Request $request): string
    {
        try {

            self::initialize();

            var_dump($request->getPostVars());
            exit;

            $newsRegisterData = $request->getPostVars();
            $newsRegisterData["imageTmpName"] = $_FILES["registerImage"]["tmp_name"] ?? "";

            self::$newsService->register($newsRegisterData);

            return json_encode([
                "success" => true,
                "message" => "Notícia cadastrada com sucesso!",
            ]);
        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }



    /**
     * Método responsável por processar a busca de uma notícia a partir do ID
     *
     * @param string $id
     * @return string
     */
    public static function getInfo(string $id): string
    {

        try {
            self::initialize();

            $data = self::$newsService->getInfo($id);


            return json_encode([
                "success" => true,
                "data" => $data,
                "message" => "Notícia encontrada!"
            ]);
        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }



    /**
     * Método responsável por processar a edição de uma notícia
     *
     * @param Request $request
     * @return string
     */
    public static function edit(Request $request): string
    {
        try {

            self::initialize();

            $newsEditData = $request->getPostVars();
            $newsEditData["imageTmpName"] = $_FILES["editImage"]["tmp_name"] ?? "";


            self::$newsService->edit($newsEditData);


            return json_encode([
                "success" => true,
                "message" => "Notícia editada com sucesso!"
            ]);
        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }



    /**
     * Método responsável por processar a deleção de uma notícia
     *
     * @param string $id
     * @return string
     */
    public static function delete(string $id): string
    {
        try {

            self::initialize();


            self::$newsService->delete($id);


            return json_encode([
                "success" => true,
                "message" => "Notícia excluída com sucesso!"
            ]);
        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }



    /**
     * Método responsável processar a mudança do status de destaque de uma notícia
     *
     * @param Request $request
     * @return string
     */
    public static function changeHighlighted(Request $request): string
    {
        try {

            self::initialize();

            $changeHighlightedData = $request->getPostVars();

            self::$newsService->changeHighlighted($changeHighlightedData);


            return json_encode([
                "success" => true,
                "message" => "Sucesso ao alterar status de destaque da notícia!"
            ]);
        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }



    /**
     * Método responsável processar a mudança do status de visibilidade de uma notícia
     *
     * @param Request $request
     * @return string
     */
    public static function changeVisible(Request $request): string
    {
        try {

            self::initialize();

            $changeVisibleData = $request->getPostVars();

            self::$newsService->changeVisible($changeVisibleData);


            return json_encode([
                "success" => true,
                "message" => "Sucesso ao alterar status de visibilidade da notícia!"
            ]);
        } catch (Exception $e) {
            return json_encode([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }




    /**
     * Método responsável processar o upload de imagem no editor de notícias
     *
     * @return string
     */
    public static function uploadEditorImage(): string
    {
        try {

            self::initialize();

            $imageTmpName = $_FILES["upload"]["tmp_name"] ?? "";

            $url = self::$newsService->uploadEditorImage($imageTmpName);

            return json_encode([
                "url" => $url
            ]);
            
        } catch (Exception $e) {
            return json_encode([
                "error" => [
                    "message" => $e->getMessage()
                ]
            ]);
        }
    }

}
