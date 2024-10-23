<?php
namespace Framework;
use Framework\Exceptions\JsonParseException;

#[\AllowDynamicProperties]
class Payload{
    private $getParams;
    private $postParams;
    private $body;

    public function __construct(){
        $contentType = $_SERVER['CONTENT_TYPE'] ?? 'no-content';
        
        //GET
        $this->getParams = $_GET;
        unset($this->payload["target"]);

        //POST
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $this->initPost($contentType);
        }
    }

    private function initPost($contentType){
        if(str_contains($contentType, 'application/json')){
            $textPayload =  file_get_contents('php://input');
            $this->body = json_decode($this->payload, true);

            // Verifica se houve erro na decodificação do JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new JsonParseException($textPayload);
            }
        }elseif(str_contains($contentType, 'application/x-www-form-urlencoded') || str_contains($contentType, 'multipart/form-data')){
            $this->postParams = $_POST;
        }
    }

    public function get($p){
        return $this->getParams[$p] ?? null;
    }

    public function post($p){
        return $this->postParams[$p] ?? null;
    }

    public function body(){
        return $this->body;
    }

}
