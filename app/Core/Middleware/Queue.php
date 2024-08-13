<?php
namespace App\Core\Middleware;


use \App\Utils\View;
use \App\Core\Request;
use \App\Core\Response;

use Closure;
use \Exception;


class Queue {


    /**
     * Mapeamento de middlewares
     * @var array
     */
    private static $map = [];


    /**
     * Mapeamento de middlewares que serão carregados em todas as rotas
     * @var array
     */
    private static $default = [];


    /**
     * Fila de middlewares a serem executados
     * @var array
     */
    private $middlewares = [];


    /**
     * Função de execução de um controlador
     * @var Closure
     */
    private $controller;


    /**
     * Argumentos da função do controlador
     * @var array
     */
    private $controllerArgs = [];



    /**
     * Método responsável por construir a classe de fila de middlewares 
     * @param array $middlewares
     * @param Closure $controller
     * @param array $controllerArgs
     */
    public function __construct(array $middlewares, Closure $controller, array $controllerArgs) {
        $this->middlewares = array_merge(self::$default, $middlewares);
        $this->controller = $controller;
        $this->controllerArgs = $controllerArgs;
    }



    /**
     * Método responsável por definir o mapeamento de middlewares 
     * @param array $map
     * @return void
     */
    public static function setMap(array $map) {
        self::$map = $map;
    }



    /**
     * Método responsável por definir o mapeamento de middlewares padrões
     * @param array $default
     * @return void
     */
    public static function setDefault(array $default) {
        self::$default = $default;
    }


    
    /**
     * Método responsável por executar o próximo nível da fila de middlewares
     * @param Request $request
     * @return Response
     */
    public function next($request) {
        
        // VERIFICA SE A FILA DE MIDDLEWARES ESTÁ VAZIA
        if (empty($this->middlewares)) return call_user_func_array($this->controller, $this->controllerArgs);

        // EXTRAI O MIDDLEWARE DA VEZ 
        $middleware = array_shift($this->middlewares);

        // VERIFICA O MAPEAMENTO
        if (!isset(self::$map[$middleware])) {
            $internalServerError = View::render("pages/error/index", [
                "code" => 500, 
                "message" => "Erro interno"
            ]);
            throw new Exception($internalServerError, 500);
        }
       
        // PRÓXIMO
        $queue = $this;
        $next = function ($request) use($queue) {
            return $queue->next($request);
        };
        
        //EXECUTA O MIDDLEWARE DA VEZ
        return (new self::$map[$middleware])->handle($request, $next);
    }
}