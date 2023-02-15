<?php

namespace App\Http;

class Response
{
    //STATUS HTTP
    private $httpCode = 200;

    //CABEÇALHO DO RESPONSE
    private $headers = [];

    //TIPO DO CONTEÚDO RETORNADO
    private $contentType = 'text/html';

    //CONTEÚDO DO RESPONSE
    private $content;

    public function __construct($httpCode, $content, $contentType = 'text/html')
    {
        $this->httpCode = $httpCode;
        $this->content  = $content;
        $this->setContentType($contentType);
    }

    //ALTERA O CONTEÚDO DO RESPONSE
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        $this->addHeader('Content-Type', $contentType);
        
    }

    public function addHeader($key, $value)
    {
       $this->headers[$key] = $value;
    }

    public function sendHeaders()
    {
        //STATUS
        http_response_code($this->httpCode);

        //ENVIAR HEADERS
        foreach($this->headers as $key=>$value){

            header($key.':'.$value);
        }
    }

    public function sendResponse()
    {
        $this->sendHeaders();

        switch($this->contentType){
            case 'text/html':
            echo $this->content;
            exit;
            case 'application/json';
            echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }

}
