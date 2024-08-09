<?php


namespace App\Core;


class Response {
    
    /**
     * Código do status HTTP
     *
     * @var integer
     */
    private $httpCode = 200;



    /**
     * Cabeçalhos do Response
     *
     * @var array
     */
    private $headers = [];




    /**
     * Tipo do conteúdo que está sendo retornado
     *
     * @var string
     */
    private $contentType = "text/html";




    /**
     * Conteúdo do Response
     *
     * @var mixed
     */
    private $content;




    /**
     * Metódo responsável por iniciar a classe e definir os valores
     *
     * @param integer $httpCode
     * @param mixed $content
     * @param string $contentType
     */
    public function __construct($httpCode, $content, $contentType = "text/html", $cors = "*") {
        $this->httpCode = $httpCode;
        $this->content = $content;
        $this->setContentType($contentType);
        $this->addHeader("Access-Control-Allow-Origin", $cors);
    }



    /**
     * Metódo responsável por retornar o código do status HTTP
     *
     * @return integer
     */
    public function getCode() {
        return $this->httpCode;
    }



    /**
     * Metódo responsável por alterar o content type do Response
     *
     * @param string $contentType
     * @return void
     */
    public function setContentType($contentType) {
        $this->contentType = $contentType;
        $this->addHeader("Content-Type", $contentType);
    }



    /**
     * Método responsável por adicionar um registro no cabeçalho do Response
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function addHeader($key, $value) {
        $this->headers[$key] = $value;
    }



    /**
     * Método responsável por enviar os cabeçalhos ao navegador
     *
     * @return void
     */
    public function sendHeaders() {
        http_response_code($this->httpCode);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        if ($this->content !== null && is_string($this->content)) {
            $this->addHeader("Content-Length", strlen($this->content));
        }
    }



    /**
     * Método utilizado para enviar a resposta para o usuário
     * 
     * @return void
     */
    public function sendResponse() {
        //ENVIA OS HEADERS
        $this->sendHeaders();

        //ENVIA O CONTEÚDO
        echo $this->content;
        exit;
    }
}


