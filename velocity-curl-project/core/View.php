<?php

namespace App\Framework;
use Framework\Render;
use Framework\TemplateDirectoryHelper;
final class View{


    //Variavel contendo as variaveis da view
    private $dataContext = [];
    private $blockContext = [];

    //Variaveis da Request
    private $statusCode;
    private $content;

    //Variaveis de validação de frontend:
    private $errors = [];

    public $controllerDir;

    public function __construct(private Render $renderService){
    }

    public function initView($controllerDir){
        $this->controllerDir = $controllerDir;
    }

    //Função sem Encadeamento
    public function render($content = null,$data=[],$statusCode = 200){
        if(is_null($content)){
            if(!is_null($this->content) ){
                $this->execute($this->statusCode == null ? 200 : $this->statusCode);
            }else{
                //Requisição executada , mas não tem conteudo para mostrar, portanto sobrescrever código HTTP.
                http_response_code(204);
            }
        }else{
            $this->content = $content;
            $this->dataContext = array_merge($this->dataContext,$data);
            $this->execute(($statusCode == null ? 200 : $statusCode));
        }
    }


    //Funções de Encadeamento
    public function withStatusCode($statusCode = 200){
        $this->statusCode = $statusCode;
        return $this;
    }

    public function renderContent($content){
        $this->content= $content;
        return $this;
    }

    public function bindData($name,$data){
        $this->dataContext[$name] = $data;
        return $this;
    }

    private function execute($statusCode){
        ob_start();
        $this->container();
        ob_end_flush();
        http_response_code($statusCode);
        $this->unsetExternalMethods();
    }

    private function container(){
        $params = ["errors"=>$this->errors];
        $this->renderService->view($this->controllerDir,$this->content,$this->dataContext,$this->blockContext,$params);
    }

    public function setErrors($errorsContainerPart){
        if(is_array($errorsContainerPart)){
            $this->errors = $errorsContainerPart;
        }
    }



    private function unsetExternalMethods(){
        unset($this->renderContent);
        unset($this->container);
        unset($this->setErrors);
        unset($this->bindData);
        unset($this->execute);
    }


    public function setBlock($name,$template){
        $this->blockContext[$name]=$template;
    }
}
