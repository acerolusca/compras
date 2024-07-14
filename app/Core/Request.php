<?php


namespace App\Core;


class Request {


    /**
     * Método HTTP da requisição
     *
     * @var string
     */
    private $httpMethod;



    /**
     * URI da página
     *
     * @var string
     */
    private $uri;




    /**
     * Parâmetros da URL ($_GET)
     *
     * @var array
     */
    private $queryParams = [];



    /**
     * Variáveis recebidas no POST da página ($_POST)
     *
     * @var array
     */
    private $postVars = [];


    /**
     * Cabeçalhos da requisição
     *
     * @var array
     */
    private $headers = [];


    /**
     * Instância de router
     *
     * @var Router 
     */
    private $router;


    /**
     * Variáveis recebidas no corpo da requisição
     *
     * @var array
     */
    private $bodyVars = [];



    /**
     * Construtor da classe
     *
     * @param Router $router
     */
    public function __construct($router) {

        # Verificando se foram recebidas e intanciando as variaveis
        $this->router = $router;
        $this->queryParams = $_GET ?? []; 
        $this->postVars = $_POST ?? []; 
        $this->headers = getallheaders(); # Metodo nativo do php que retorna os headers da requisicao
        $this->httpMethod = $_SERVER["REQUEST_METHOD"] ?? ""; # Variavel que recebe o metodo da requisicao do server
        $this->setUri();
        $this->setBodyVars();
    }



    /**
     * Método responsável por retornar o método HTTP da requisição
     *
     * @return string
     */
    public function getHttpMethod() {
        return $this->httpMethod;
    }



    /**
     * Método responsável por retornar a URI da requisição
     *
     * @return string
     */
    public function getUri() {
        return $this->uri;
    }



    /**
     * Método responsável por retornar os headres da requisição
     *
     * @return array
     */
    public function getHeaders(){
        return $this->headers;
    }



    /**
     * Método responsável por retornar os parâmetros da URL da requisição
     *
     * @return array
     */
    public function getQueryParams() {
        return $this->queryParams;
    }



    /**
     * Método responsável por retornar as variáveis POST da requisição
     *
     * @return array
     */
    public function getPostVars() {
        return $this->postVars;
    }



    /**
     * Método responsável por retornar as variáveis do corpo da requisição
     *
     * @return array
     */
    public function getBodyVars() {
        return $this->bodyVars;
    }



    /**
     * Método responsável por retornar uma instância de Router
     *
     * @return Router
     */
    public function getRouter() {
        return $this->router;
    }



    /**
     * Método responsável por atribuir a URI da requisição
     *
     * @return void
     */
    private function setUri() {
        $this->uri = $_SERVER["REQUEST_URI"] ?? "";
        $xURI = explode("?", $this->uri);
        $this->uri = $xURI[0];
    }


    /**
     * Método responsável por atribuir as variáveis recebidas no corpo da requisição
     *
     * @return void
     */
    private function setBodyVars() {
        $this->bodyVars = (array) json_decode(file_get_contents('php://input'));
    }


    


}