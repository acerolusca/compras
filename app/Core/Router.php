<?php

namespace App\Core;


use \Closure;
use \Exception;
use \ReflectionFunction;
use \App\Utils\View;
use \App\Core\Middleware\Queue as MiddlewareQueue;


class Router {

    /**
     * URL completa do projeto (raíz)
     *
     * @var string
     */
    private $url = "";




    /**
     * Prefixo de todas as rotas
     * 
     * @var string
     */

    private $prefix = "";




    /**
     * Índice rotas
     *
     * @var array
     */
    private $routes = [];




    /**
     * Instância de Request
     *
     * @var Request
     */
    private $request
    ;




    /**
     * Método responsável por inciciar a classe
     *
     * @param string $url
     */
    public function __construct($url) {
        $this->url = $url;
        $this->request = new Request($this);
        $this->setPrefix();
    }



    /**
     * Metódo responsável por definir o prefixo das rotas
     *
     * @return void
     */
    private function setPrefix() {

        //INFORMAÇÔES DA URL ATUAL
        $parseUrl = parse_url($this->url);

        //DEFINE PREFIXO
        $this->prefix = $parseUrl["path"] ?? "";
    }



    /**
     * Método responsável por adicionar uma rota na classe
     *
     * @param string $method
     * @param string $route
     * @param array $params
     * @return void
     */
    private function addRoute($method, $route, $params = []) {

        //VALIDAÇÃO DOS PARÂMETROS
        foreach($params as $key => $value) {
            if ($value instanceof Closure) {
                $params["controller"] = $value;
                unset($params[$key]);
            }
        }

       
        //DEFINIÇÃO DOS MIDDLEWARES
        $params["middlewares"] ??= [];

        $params["variables"] = [];


        //PADRÃO DE VALIDAÇÃO DAS VARIÁVEIS DAS ROTAS
        $patternVariable = "/{(.*?)}/";
        if (preg_match_all($patternVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, "(.*?)", $route);
            $params["variables"] = $matches[1];
        }
 
        //PADRÃO DE VALIDAÇÃO DA URL
        $patternRoute = "/^".str_replace("/", "\/", $route)."$/";
        

        //ADICIONA A ROTA DENTRO DA CLASSE
        $this->routes[$patternRoute][$method] = $params;

    }



    /**
     * Método responsável por retonar a URI desconsiderando o prefixo
     *
     * @return string
     */
    private function getUri() {
        //URI DA REQUEST
        $uri = $this->request->getUri();

        //FATIA A URI COM O PREFIXO
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

        //RETORNA A URI SEM PREFIXO
        return end($xUri);
    }



    /**
     * Método responsável por retornar os dados da rota atual
     *
     * @return array
     */
    private function getRoute() {

        //URI
        $uri = $this->getUri();

        //MÉTODO HTTP
        $httpMethod = $this->request->getHttpMethod();

        //VALIDA AS ROTAS
        foreach($this->routes as $patternRoute => $methods) {

            //VERIFICA SE A URI BATE COM O PADRÃO
            if (preg_match($patternRoute, $uri, $matches)) {

                //VERIFICA O MÉTODO
                if (isset($methods[$httpMethod])) {
                    unset($matches[0]);

                    $keys = $methods[$httpMethod]["variables"];
                    $methods[$httpMethod]["variables"] = array_combine($keys, $matches);
                    $methods[$httpMethod]["variables"]["request"] = $this->request; 
                    
                    //RETORNO DOS PARÂMETROS DA ROTA
                    return $methods[$httpMethod];
                }

                //MÉTODO NÃO PERMITIDO
                $notAllowedMethod = View::render("pages/error/index", [
                    "code" => 405, 
                    "message" => "Método não permitido"
                ]);
                
                throw new Exception( $notAllowedMethod, 405);
            }

        }

        //URL NÃO ENCONTRADA
        $notFoundPage = View::render("pages/error/index", [
            "code" => 404, 
            "message" => "Página não encontrada"
        ]);

        throw new Exception($notFoundPage, 404);
    }



    /**
     * Método responsável por definir uma rota de GET
     *
     * @param string $route
     * @param array $params
     * @return void
     */
    public function get($route, $params) {
        return $this->addRoute("GET", $route, $params);
    }



    /**
     * Método responsável por definir uma rota de POST
     *
     * @param string $route
     * @param array $params
     * @return void
     */
    public function post($route, $params) {
        return $this->addRoute("POST", $route, $params);
    }



    /**
     * Método responsável por definir uma rota de PUT
     *
     * @param string $route
     * @param array $params
     * @return void
     */
    public function put($route, $params) {
        return $this->addRoute("PUT", $route, $params);
    }



    /**
     * Método responsável por definir uma rota de DELETE
     *
     * @param string $route
     * @param array $params
     * @return void
     */
    public function delete($route, $params) {
        return $this->addRoute("DELETE", $route, $params);
    }



    /**
     * Método responsável por redirecionar o usuário
     *
     * @param string $route
     * @return void
     */
    public function redirect($route) {
        $url = $this->url . $route;
        header("Location: " . $url);
        exit();
    }



    /**
     * Método responsável por executar a rota atual
     *
     * @return Response
     */
    public function run() {
        try {

            //OBTÉM A ROTA ATUAL
            $route = $this->getRoute();

            if (!isset($route["controller"])) {
                $notFoundPage = View::render("pages/error/index", [
                    "code" => 404, 
                    "message" => "Página não encontrada"
                ]);
                throw new Exception($notFoundPage, 404);
            }
            
            $args = [];
            $reflectionFunc = new ReflectionFunction($route["controller"]);
            
            foreach($reflectionFunc->getParameters() as $parameter) {
                $name = $parameter->getName();
                $args[$name] = $route["variables"][$name] ?? "";
            }

            return (new MiddlewareQueue($route['middlewares'], $route["controller"], $args))->next($this->request);

            //return call_user_func_array($route["controller"], $args);
            
        } catch(Exception $e) {
            return new Response($e->getCode(), $e->getMessage());
        }
    }
}