<?php
namespace App\Framework;

use Framework\RouterService;
use Framework\Render;

use Framework\Exceptions\ControllerDoubleInitializationException;
use Framework\Exceptions\ActionNotAllowedException;
use InvalidArgumentException;
use Exception;


//Classe de Framework Abstrata para os controladores
abstract class Controller{
    //Namespace
    const CONTROLLERS_NAMESPACE = "App\\Controllers";

    //Função de Handle
    protected $handleMessages;

    //Variaveis
    protected $view;
    protected $render;
    private $messages = [];
    private $initialized = false;

    final protected function goToAction($name,$action,$params = []){
        $targetController = self::CONTROLLERS_NAMESPACE ."\\".$name;
        if(class_exists($targetController) && method_exists($targetController, $action)){
           try{
                //Recuperando instancia global para uso do ServiceProvider
                global $myApp;
                $targetController = $myApp->serviceProvider->container->make($targetController);

                call_user_func_array([$targetController, $action], $params);
           }catch(InvalidArgumentException $e){
                throw new InvalidArgumentException('Esta é uma mensagem de exceção.');
           }
        }else{
            throw new ActionNotAllowedException($targetController,$action);
        }
    }

    private function getCurrentPath(){
        $className = get_called_class();
        // Obtém o caminho completo do arquivo da classe filha
        $reflectionClass = new \ReflectionClass($className);
        return dirname($reflectionClass->getFileName(),2);
    }

    final public function initController($sp){
        if(!$this->initialized){
            $this->view = $sp->container->make('View');

            $this->view->initView($this->getCurrentPath());
            $this->render = $sp->container->get("Render");
            $this->handleMessages= function ($messagesContainer){
                $this->setMessages($messagesContainer["messages"]);
                $this->view->setErrors($messagesContainer["errors"]);
            };
        }else{
            throw new ControllerDoubleInitializationException();
        }
    }

    final public function recieveMessages($messagesContainer){
        if(isset($this->handleMessages)){
            ($this->handleMessages)($messagesContainer);
            unset($this->handleMessages);
        }
    }

    private function setMessages($errorsContainerPart){
        if(is_array($errorsContainerPart)){
            $this->messages = $errorsContainerPart;
        }
    }

    public function getMessage($id){
        return $this->messages["messages"][$id] ?? false;
    }

    public function getError($id){
        return $this->messages["errors"][$id] ?? false;
    }

    public function response(){
        return $this->render;
    }
}
