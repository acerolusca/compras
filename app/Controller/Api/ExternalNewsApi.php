<?php

namespace App\Controller\Api;

use App\Service\NewsService;
use App\Repository\NewsRepository;
use App\Core\Request;
use App\Core\Response;
use App\Database\Database;
use Firebase\JWT\JWT;

use OpenApi\Annotations as OA;

use \Exception;



/**
 * @OA\Info(
 *     title="API COMPRAS",
 *     version="1.0"
 * )
 */
class ExternalNewsApi
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
     * \Get(
     *     path="/api/authorization",
     *     summary="Retorna token JWT para acessar API's de notícia",
     *     description="Essa API retorna um token JWT para acessar API's de notícia. O token expira em 24 horas a contar do instante de sua geração",
     *     operationId="getAuthorization",
     *     tags={"Token JWT"},
     * 
     *     
     *     \Response(
     *         response=200,
     *         description="Token gerado com sucesso.",
     *         \JsonContent(
     *             type="object",
     *             \Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             \Property(
     *                 property="data",
     *                 type="string",
     *                 example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzM4NCJ9.eyJpc3MiOiJsb2NhbGhvc3QiLCJuYmYiOjE3MjM0ODczNDIsImV4cCI6MTcyMzU3Mzc0MiwiaWF0IjoxNzIzNDg3MzQyLCJhcHAiOiJjb21wcmFzX21hIiwiaWQiOiI2NmJhNTQ2ZWU4YzE2In0.yQU7EK0izpl4ObCv3Yu243NXrw1YKU2THI-twRH8eapJdhLxnfXNgebXJeUop25O"
     *             ),
     *             \Property(
     *                 property="message",
     *                 type="string",
     *                 example="Token gerado com sucesso."
     *             )
     *         )
     *     ),
     * 
     *     \Response(
     *         response=401,
     *         description="Acesso negado.",
     *         \JsonContent(
     *             type="object",
     *             \Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             \Property(
     *                 property="data",
     *                 type="string",
     *                 example=""
     *             ),
     *             \Property(
     *                 property="message",
     *                 type="string",
     *                 example="Acesso negado."
     *             )
     *         )
     *     )
     * )  
     * 
     * @return Response
     */
    public static function getAuthorization(): Response
    {
        try {

            $key = getenv("JWT_KEY");
            $now = time();
            $payload = [
                "iss" => getenv("JWT_ISS"),
                "nbf" => $now,
                "exp" => $now + 86400,
                "iat" => $now,
                "app" => getenv("JWT_APP"),
                "id" => uniqid()
            ];

            $jwt = JWT::encode($payload, $key, 'HS384');

            $response = json_encode([
                "success" => true,
                "data" => $jwt,
                "message" => "Token gerado com sucesso."
            ]);

            return new Response(200, $response, "application/json");
        } catch (Exception $e) {

            $response = json_encode([
                "success" => false,
                "data" => "",
                "message" => $e->getMessage()
            ]);

            return new Response($e->getCode(), $response, "application/json");
        }
    }



    /**
     * @OA\Get(
     *     path="/api/news/all",
     *     summary="Retorna todas as notícias cadastradas disponíveis",
     *     description="API responsável por retornar todas as notícias cadastradas disponíveis, caso haja alguma.",
     *     operationId="getAllAvailable",
     *     tags={"Notícias"},
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso na operação, mesmo se não houver notícias cadastradas disponíveis.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true,
     *                 description="Booleano com valor de 'true' indicando sucesso na operação."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="array",
     *                         description="Array de objetos com informações das notícias cadastradas disponíveis, se houver alguma.",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1, description="Identificador da notícia."),
     *                             @OA\Property(property="title", type="string", maxLength=100, example="Título da notícia A", description="Título da notícia com mínimo de 10 e máximo de 100 caracteres."),
     *                             @OA\Property(property="summary", type="string", maxLength=250, example="Resumo da notícia", description="Resumo da notícia com mínimo de 100 e máximo de 150 caracteres."),
     *                             @OA\Property(property="image", type="string", example="https://compras/image/news/default-image-path.svg", description="Caminho para a imagem principal da notícia."),
     *                             @OA\Property(property="date", type="string", format="date-time", example="12/08/2024 14:00:00", description="Data e hora da publicação da notícia em formato brasileiro."),
     *                             @OA\Property(property="category", type="string", example="Notícias", description="Categoria da notícia.")
     *                         )
     *                     ),
     *                     @OA\Schema(
     *                         type="string",
     *                         example="",
     *                         description="String vazia caso não haja notícias cadastradas disponíveis."
     *                     )
     *                 }
     *             ),
     * 
     *             
     *             @OA\Property(
     *                 property="message",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Notícias carregadas com sucesso.",
     *                         description="Mensagem de sucesso caso haja notícias cadastradas disponíveis.",
     *                     ),
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Não há notícias disponíveis no momento.",
     *                         description="Mensagem indicando que não há notícias cadastradas disponíveis."
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     * 
     *     
     *     @OA\Response(
     *         response=401,
     *         description="Acesso negado. Ocorre se a requisição for originada de um servidor não autorizado.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Booleano com valor de 'false' indicando erro na operação."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 example="",
     *                 description="String vazia, uma vez que não há dados para retonar."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Acesso negado.",
     *                 description="Mensagem de acesso negado indicando que a requisição foi feita por um servidor não autorizado."
     *             )
     *         )
     *     ),
     *       
     *     
     *     @OA\Response(
     *         response=503,
     *         description="Serviço indisponível ou erro ao carregar notícias. Ocorre em caso de falha na conexão com o banco de dados ou na execução de uma consulta.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Booleano com valor de 'false' indicando erro na operação."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 example="",
     *                 description="String vazia, uma vez que não há dados para retonar."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Serviço indisponível.",
     *                         description="Mensagem de serviço indisponível, indicando que houve falha na conexão com o banco de dados.",
     *                     ),
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Erro ao carregar notícias.",
     *                         description="Mensagem de erro ao carregar notícias, indicando que houve algum problema com a query que retorna os dados das notícias."
     *                     )
     *                 }                
     *             )
     *         )
     *     )
     * )
     * 
     * @return Response
     */
    public static function getAllAvailable(): Response
    {
        try {

            self::initialize();

            $data = self::$newsService->getAllAvailable();

            $response = json_encode([
                "success" => true,
                "data" => count($data) == 0 ? "" : $data,
                "message" => count($data) == 0 ? "Não há notícias disponíveis no momento." : "Notícias carregadas com sucesso."
            ]);

            return new Response(200, $response, "application/json");
        } catch (Exception $e) {

            $response = json_encode([
                "success" => false,
                "data" => "",
                "message" => $e->getMessage()
            ]);

            return new Response($e->getCode(), $response, "application/json");
        }
    }



    /**
     * @OA\Get(
     *     path="/api/news/highlighted",
     *     summary="Retorna todas as notícias em destaque disponíveis",
     *     description="API responsável por retornar todas as notícias em destaque disponíveis, caso haja alguma.",
     *     operationId="getHighlightedAvailable",
     *     tags={"Notícias"},
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso na operação, mesmo que ainda não haja notícias em destaque disponíveis.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true,
     *                 description="Booleano com valor de 'true' indicando sucesso na operação."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="array",
     *                         description="Array de objetos com informações das notícias em destaque disponíveis, se houver alguma.",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1, description="Identificador da notícia."),
     *                             @OA\Property(property="title", type="string", maxLength=100, example="Título da notícia A", description="Título da notícia com mínimo de 10 e máximo de 100 caracteres."),
     *                             @OA\Property(property="summary", type="string", maxLength=250, example="Resumo da notícia", description="Resumo da notícia com mínimo de 100 e máximo de 150 caracteres."),
     *                             @OA\Property(property="image", type="string", example="https://compras/image/news/default-image-path.svg", description="Caminho para a imagem principal da notícia."),
     *                             @OA\Property(property="date", type="string", format="date-time", example="12/08/2024 14:00:00", description="Data e hora da publicação da notícia em formato brasileiro."),
     *                             @OA\Property(property="category", type="string", example="Destaques", description="Categoria da notícia.")
     *                         )
     *                     ),
     *                     @OA\Schema(
     *                         type="string",
     *                         example="",
     *                         description="String vazia caso não haja notícias em destaque disponíveis."
     *                     )
     *                 }
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Notícias em destaque carregadas com sucesso.",
     *                         description="Mensagem de sucesso caso haja notícias em destaque disponíveis.",
     *                     ),
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Não há notícias em destaque disponíveis no momento.",
     *                         description="Mensagem indicando que não há notícias em destaque disponíveis."
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     * 
     *     
     *     @OA\Response(
     *         response=401,
     *         description="Acesso negado. Ocorre se a requisição for originada de um servidor não autorizado.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Booleano com valor de 'false' indicando erro na operação."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 example="",
     *                 description="String vazia, uma vez que não há dados para retonar."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Acesso negado.",
     *                 description="Mensagem de acesso negado indicando que a requisição foi feita por um servidor não autorizado."
     *             )
     *         )
     *     ),
     *       
     *     
     *     @OA\Response(
     *         response=503,
     *         description="Serviço indisponível ou erro ao carregar notícias. Ocorre em caso de falha na conexão com o banco de dados ou na execução de uma consulta.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Booleano com valor de 'false' indicando erro na operação."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 example="",
     *                 description="String vazia, uma vez que não há dados para retonar."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Serviço indisponível.",
     *                         description="Mensagem de serviço indisponível, indicando que houve falha na conexão com o banco de dados.",
     *                     ),
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Erro ao carregar notícias.",
     *                         description="Mensagem de erro ao carregar notícias, indicando que houve algum problema com a query que retorna os dados das notícias."
     *                     )
     *                 }                
     *             )
     *         )
     *     )
     * )
     * 
     * @return Response
     */
    public static function getHighlightedAvailable(): Response
    {
        try {

            self::initialize();

            $data = self::$newsService->getHighlightedAvailable();

            $response = json_encode([
                "success" => true,
                "data" => count($data) == 0 ? "" : $data,
                "message" => count($data) == 0 ?
                    "Não há notícias em destaque disponíveis no momento." :
                    "Notícias em destaque carregadas com sucesso."
            ]);

            return new Response(200, $response, "application/json");
        } catch (Exception $e) {
            $response = json_encode([
                "success" => false,
                "data" => "",
                "message" => $e->getMessage()
            ]);

            return new Response($e->getCode(), $response, "application/json");
        }
    }



    /**
     * @OA\Get(
     *     path="/api/news/regular",
     *     summary="Retorna todas as notícias regulares disponíveis",
     *     description="API responsável por retornar todas as notícias regulares disponíveis, caso haja alguma..",
     *     operationId="getRegularAvailable",
     *     tags={"Notícias"},
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso na operação, mesmo que ainda não haja notícias regulares disponíveis.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true,
     *                 description="Booleano com valor de 'true' indicando sucesso na operação."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="array",
     *                         description="Array de objetos com informações das notícias regulares disponíveis, se houver alguma.",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1, description="Identificador da notícia."),
     *                             @OA\Property(property="title", type="string", maxLength=100, example="Título da notícia A", description="Título da notícia com mínimo de 10 e máximo de 100 caracteres."),
     *                             @OA\Property(property="summary", type="string", maxLength=250, example="Resumo da notícia", description="Resumo da notícia com mínimo de 100 e máximo de 150 caracteres."),
     *                             @OA\Property(property="image", type="string", example="https://compras/image/news/default-image-path.svg", description="Caminho para a imagem principal da notícia."),
     *                             @OA\Property(property="date", type="string", format="date-time", example="12/08/2024 14:00:00", description="Data e hora da publicação da notícia em formato brasileiro."),
     *                             @OA\Property(property="category", type="string", example="Notícias", description="Categoria da notícia.")
     *                         )
     *                     ),
     *                     @OA\Schema(
     *                         type="string",
     *                         example="",
     *                         description="String vazia caso não haja notícias regulares disponíveis."
     *                     )
     *                 }
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Notícias regulares carregadas com sucesso.",
     *                         description="Mensagem de sucesso caso haja notícias regulares disponíveis.",
     *                     ),
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Não há notícias regulares disponíveis no momento.",
     *                         description="Mensagem indicando que não há notícias regulares disponíveis."
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     * 
     *     
     *     @OA\Response(
     *         response=401,
     *         description="Acesso negado. Ocorre se a requisição for originada de um servidor não autorizado.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Booleano com valor de 'false' indicando erro na operação."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 example="",
     *                 description="String vazia, uma vez que não há dados para retonar."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Acesso negado.",
     *                 description="Mensagem de acesso negado indicando que a requisição foi feita por um servidor não autorizado."
     *             )
     *         )
     *     ),
     *       
     *     
     *     @OA\Response(
     *         response=503,
     *         description="Serviço indisponível ou erro ao carregar notícias. Ocorre em caso de falha na conexão com o banco de dados ou na execução de uma consulta.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Booleano com valor de 'false' indicando erro na operação."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 example="",
     *                 description="String vazia, uma vez que não há dados para retonar."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Serviço indisponível.",
     *                         description="Mensagem de serviço indisponível, indicando que houve falha na conexão com o banco de dados.",
     *                     ),
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Erro ao carregar notícias.",
     *                         description="Mensagem de erro ao carregar notícias, indicando que houve algum problema com a query que retorna os dados das notícias."
     *                     )
     *                 }                
     *             )
     *         )
     *     )
     * )
     * 
     * @return Response
     */
    public static function getRegularAvailable(): Response
    {
        try {

            self::initialize();

            $data = self::$newsService->getRegularAvailable();

            $response = json_encode([
                "success" => true,
                "data" => count($data) == 0 ? "" : $data,
                "message" => count($data) == 0 ?
                    "Não há notícias regulares disponíveis no momento." :
                    "Notícias regulares carregadas com sucesso."
            ]);

            return new Response(200, $response, "application/json");
        } catch (Exception $e) {

            $response = json_encode([
                "success" => false,
                "data" => "",
                "message" => $e->getMessage()
            ]);

            return new Response($e->getCode(), $response, "application/json");
        }
    }



    /**
     * @OA\Get(
     *     path="/api/news/info/{id}",
     *     summary="Retorna as informações de uma notícia específica disponível",
     *     description="API responsável por retornar as informações de uma notícia específica, de acordo com o ID.",
     *     operationId="getInfoAvailable",
     *     tags={"Notícias"},
     * 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="ID da notícia. Necessário que seja numérico.",
     *         example = "10"
     *     ),
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso na operação.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true,
     *                 description="Booleano com valor de 'true' indicando sucesso na operação."                 
     *             ),
     * 
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1, description="Identificador da notícia."),
     *                 @OA\Property(property="title", type="string", maxLength=100, example="Título da notícia A", description="Título da notícia com mínimo de 10 e máximo de 100 caracteres."),
     *                 @OA\Property(property="summary", type="string", maxLength=250, example="Resumo da notícia", description="Resumo da notícia com mínimo de 100 e máximo de 150 caracteres."),
     *                 @OA\Property(property="body", type="string", example="<p>Corpo da notícia</p>", description="Corpo da notícia com tags HTML"), 
     *                 @OA\Property(property="image", type="string", example="https://compras/image/news/default-image-path.svg", description="Caminho para a imagem principal da notícia."),
     *                 @OA\Property(property="date", type="string", format="date-time", example="12/08/2024 14:00:00", description="Data e hora da publicação da notícia em formato brasileiro."),
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Notícia carregada com sucesso.",
     *                 description="Mensagem de sucesso indicando que a notícia foi encontrada."
     *             )
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=400,
     *         description="Parâmetro de notícia inválido ou não informado. Ocorre se ID enviado como parâmetro for não numérico ou vazio.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Booleano com o valor de 'false' indicando erro na operação."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 example="",
     *                 description="String vazia uma vez que não há dados para retornar"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Parâmetro de notícia inválido ou não informado.",
     *                 description="Mensagem de erro indicando algum problema com o ID enviado como parâmetro."
     *             )
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=401,
     *         description="Acesso negado. Ocorre se a requisição for originada de um servidor não autorizado.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Booleano com valor de 'false' indicando erro na operação."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 example="",
     *                 description="String vazia, uma vez que não há dados para retonar."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Acesso negado.",
     *                 description="Mensagem de erro indicando que a requisição foi feita por um servidor não autorizado."
     *             )
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=404,
     *         description="Notícia não está disponível. Ocorre quando ID enviado como parâmetro não corresponde a uma notícia disponível.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Booleano com valor de 'false' indicando erro na operação."               
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 example="",
     *                 description="String vazia, uma vez que não há dados para retonar."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Notícia não está disponível.",
     *                 description="Mensagem de erro indicando que não há notícia disponível correspondente ao ID enviado como parâmetro."
     *             )
     *         )
     *     ),
     *       
     *     
     *     @OA\Response(
     *         response=503,
     *         description="Serviço indisponível ou erro ao carregar notícia. Ocorre em caso de falha na conexão com o banco de dados ou na execução de uma consulta.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Booleano com valor de 'false' indicando erro na operação."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 example="",
     *                 description="String vazia, uma vez que não há dados para retonar."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Serviço indisponível.",
     *                         description="Mensagem de serviço indisponível, indicando que houve falha na conexão com o banco de dados.",
     *                     ),
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Erro ao carregar notícias.",
     *                         description="Mensagem de erro ao carregar notícia, indicando que houve algum problema com a query que retorna os dados da notícia."
     *                     )
     *                 }                
     *             )
     *         )
     *     )
     * )
     * 
     * @param string $id
     * @return Response
     */
    public static function getInfoAvailable(string $id): Response
    {
        try {

            self::initialize();

            $data = self::$newsService->getInfoAvailable($id);

            $response = json_encode([
                "success" => true,
                "data" => $data,
                "message" => "Notícia carregada com sucesso."
            ]);

            return new Response(200, $response, "application/json");
        } catch (Exception $e) {

            $response = json_encode([
                "success" => false,
                "data" => "",
                "message" => $e->getMessage()
            ]);

            return new Response($e->getCode(), $response, "application/json");
        }
    }



    /**
     * @OA\Get(
     *     path="/api/news/search/{searched}",
     *     summary="Retorna todas as notícias disponíveis correspondentes à busca",
     *     description="API responsável por retornar as informações de todas as notícias disponíveis correspondentes à busca.",
     *     operationId="search",
     *     tags={"Notícias"},
     * 
     *     @OA\Parameter(
     *         name="searched",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Parâmetro de busca. Pode ser um fragmento do título, do resumo ou da data de publicação da notícia",
     *         example = "Título da notícia B"
     *     ),
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso na operação, mesmo que não seja encontrada correspondência entre parâmetro de busca e notícias disponíveis.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true,
     *                 description="Booleano o com valor de 'true' indicando sucesso na operação."
     *             ),
     * 
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="array",
     *                         description="Array de objetos com as informações das notícias correspondentes à busca, se houver alguma.",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1, description="Identificador da notícia."),
     *                             @OA\Property(property="title", type="string", maxLength=100, example="Título da notícia A", description="Título da notícia com mínimo de 10 e máximo de 100 caracteres."),
     *                             @OA\Property(property="summary", type="string", maxLength=250, example="Resumo da notícia", description="Resumo da notícia com mínimo de 100 e máximo de 150 caracteres."),
     *                             @OA\Property(property="image", type="string", example="https://compras/image/news/default-image-path.svg", description="Caminho para a imagem principal da notícia."),
     *                             @OA\Property(property="date", type="string", format="date-time", example="12/08/2024 14:00:00", description="Data e hora da publicação da notícia em formato brasileiro."),
     *                             @OA\Property(property="category", type="string", example="Notícias", description="Categoria da notícia.")
     *                         )
     *                     ),
     *                     @OA\Schema(
     *                         type="string",
     *                         example="",
     *                         description="String vazia se não houver correspondência entre o parâmetro de busca e as notícias disponíveis"
     *                     )
     *                 }
     *             ),
     * 
     *             @OA\Property(
     *                 property="message",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Notícias correspondentes à busca carregadas com successo.",
     *                         description="Mensagem de sucesso caso haja alguma notícia disponível correspondente ao parâmetro de busca.",
     *                     ),
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Não há notícias correspondentes à busca.",
     *                         description="Mensagem indicando que não há notícias disponíveis correspondentes ao parâmetro de busca.",
     *                     )
     *                 }           
     *             )
     *         )
     *     ),
     * 
     * 
     *     @OA\Response(
     *         response=400,
     *         description="Parâmetro de busca inválido ou não informado. Ocorre quando o parâmetro de busca for vazio.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 example=""
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Parâmetro de busca inválido ou não informado."
     *             )
     *         )
     *     ),
     * 
     *     
     *     @OA\Response(
     *         response=401,
     *         description="Acesso negado. Ocorre se a requisição for originada de um servidor não autorizado.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Booleano com valor de 'false' indicando erro na operação."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 example="",
     *                 description="String vazia, uma vez que não há dados para retonar."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Acesso negado.",
     *                 description="Mensagem de erro indicando que a requisição foi feita por um servidor não autorizado."
     *             )
     *         )
     *     ),
     *       
     *     
     *     @OA\Response(
     *         response=503,
     *         description="Serviço indisponível ou erro ao carregar notícias. Ocorre em caso de falha na conexão com o banco de dados ou na execução de uma consulta.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Booleano com valor de 'false' indicando erro na operação."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 example="",
     *                 description="String vazia, uma vez que não há dados para retonar."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Serviço indisponível.",
     *                         description="Mensagem de serviço indisponível, indicando que houve falha na conexão com o banco de dados.",
     *                     ),
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Erro ao carregar notícias.",
     *                         description="Mensagem de erro ao carregar notícias, indicando que houve algum problema com a query que retorna os dados das notícias."
     *                     )
     *                 }                
     *             )
     *         )
     *     )
     * )
     * 
     * @param string $searched
     * @return Response
     */
    public static function search(string $searched): Response
    {
        try {
        
            self::initialize();

            $data = self::$newsService->search($searched);

            $response = json_encode([
                "success" => true,
                "data" => count($data) == 0 ? "" : $data,
                "message" => count($data) == 0 ?
                    "Não há notícias correspondentes à busca." :
                    "Notícias correspondentes à busca carregadas com successo."
            ]);

            return new Response(200, $response, "application/json");

        } catch (Exception $e) {

            $response = json_encode([
                "success" => false,
                "data" => "",
                "message" => $e->getMessage()
            ]);

            return new Response($e->getCode(), $response, "application/json");
        }
    }
}
