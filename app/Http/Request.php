<?php

namespace App\Http;

class Request{

    //INSTÂNCIA DE ROUTER
    private $router;

    //MÉTODO DA REQUEST
    private $httpMethod;

    //URI DA PÁGINA
    private $uri;

    //PARAMATROS DA URL GET
    private $queryParams = [];

    //VARIÁVEIS RECEBIDAS VIA POST
    private $postVars = [];

    //CABEÇALHOS DA REQUISIÇÃO
    private $headers = [];

    public function __construct($router)
    {
        $this->router        = $router;
        $this->httpMethod    = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->setURI();
        $this->queryParams   = $_GET ?? [];
        $this->headers       = getallheaders(); // método nativo do PHP
        $this->setPostVars();
    }

    /**
     * Define as variaveis do POST
     */
    public function setPostVars()
    {
        //VERIFICA O MEÉTODO DA REQUISIÇÃO, O GET É O UNICO QUE NÃO RECEBE CORPO 
        if ($this->httpMethod == 'GET') return false;

        //POST PADRÃO
        $this->postVars = $_POST ?? [];

        //POST JSON
        $inputRaw = file_get_contents('php://input');

        //se existir, retorna o body, se não o padrão
        $this->postVars = (strlen($inputRaw) && empty($_POST)) ? json_decode($inputRaw, true) : $this->postVars;

    }

    public function setURI()
    {
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';
        $xURI = explode('?', $this->uri);
        $this->uri = $xURI[0];
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    public function getUri()
    {
        return $this->uri;
    }
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    public function getPostVars()
    {
        return $this->postVars;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
