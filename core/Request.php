<?php

namespace App\Framework;
use App\Framework\IRequest;
use App\Framework\Session\ISessionManager;
use Framework\Payload;

class Request implements IRequest{
    private $url;
    private $targetUrl;
    private $payload;
    private $method;
    public function __construct(private ISessionManager $sm){
        $this->targetUrl = rtrim($_GET["target"], '/');
        $this->method = $_SERVER['REQUEST_METHOD'];

        $this->payload = new Payload();
    }


    public function getMethod(){
        return $this->method;
    }

    public function payload(){
        return $this->payload;
    }

    public function getUrl(){
        return $this->url;
    }

    public function getTarget(){
        return $this->targetUrl;
    }

    public function getSession(){
        return $this->sm;
    }

    public function post($name = false){
        if($name){
            return $_POST[$name] ?? false;
        }
        return $_POST;
    }

    public function get($name = false){
        if($name){
            return $_GET[$name] ?? false;
        }
        return $_GET;
    }

    public function file($fileName){
        if(
            isset($_FILES[$fileName]) &&
            !$_FILES[$fileName]['error'] == UPLOAD_ERR_NO_FILE &&
            $_FILES[$fileName]["error"] == 0
        ){
            return $_FILES[$fileName];
        }
        return false;
    }
}
