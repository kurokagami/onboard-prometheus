<?php
namespace Framework\Routing;

use Framework\Utils\HttpStatusCode;
use Doctrine\Common\Annotations\AnnotationReader;
use App\Framework\Route;
use App\Framework\Routes\Root;
use App\Framework\Routes\Get;
use App\Framework\Routes\Post;
use App\Framework\Routes\NotFound;
use App\Framework\Routing\IRouteManagerService;
use Framework\Interfaces\IServiceProvider;
use Framework\Exceptions\NotFoundHttpException;
use App\Middleware;
use Exception;
use DI\Annotation\Scope;


#[\AllowDynamicProperties]
final class RouteManagerService implements IRouteManagerService{

    const CONTROLLERS_NAMESPACE = "App\\Controllers";

    private $selfUrl;
    private $availableRoutes;

    public function __construct(){
        //Url propria
        if (isset($_ENV['SELF_URL']) && $_ENV['SELF_URL'] !== '') {
            $this->selfUrl =  rtrim($_ENV['SELF_URL'], '/');
        }
    }

    public function setRoutesCollection($collection){
        $this->availableRoutes = $collection;
    }

    public function setBaseUrl($basePath){
        $this->selfUrl = RouterHelper::sanitizeURL($this->selfUrl."/".$basePath);
    }

    //Função de redirecionamento (somente se a rota existir no controller MVC)
    public function safeRedirect($target,$method = "GET"){
        //Limpeza do Target sem variaveis get
        $targetSanitized = RouterHelper::sanitizeURL($target);
        if(RouterHelper::canNavigate($this->availableRoutes,"{$method}_{$targetSanitized}")){
            header("Location: {$this->selfUrl}/{$target}");
            exit();
        }else{
            throw new NotFoundHttpException("Rota buscada não encontrada: {$target}");
        }
    }

    //Função de redirecionamento cega
    public function redirect($target){
        header("Location:".RouterHelper::sanitizeURL("{$this->selfUrl}/{$target}"));
        exit();
    }

//Build complexo de redirect
    public function redirectBuild(){
        //Criar variaveis globais temporarias para tal
        $this->redirectBuildTarget = "";
        
        $this->variables = [];
        $this->errorVariables = [];

        $this->_with = fn($varName,$value)=>(
            $this->variables[$varName] = $value
        );

        $this->_withError = function($varName,$value){
            $this->errorVariables[$varName] = $value;
            return $this;
        };
     

        $this->_to = fn($target) =>(
            $this->redirectBuildTarget = $target
        );


        $that = $this;
        $this->_execute = function() use (&$that){
            //Copias Locais
            $redirectBuildTarget = $that->redirectBuildTarget;
            $variables = $that->variables;
            $errorVariables = $that->errorVariables;
            //Remover variaveis globais da memoria
            unset($that->redirectBuildTarget);
            unset($that->variables);
            unset($that->errorVariables);

            //Remover metodos globais anonimos ja usados
            unset($this->_to);
            unset($this->_with);
            unset($this->_withError);
            unset($this->_execute);

            $_SESSION["FRAMEWORK_ROUTE_VARIABLES"] = $variables;
            $_SESSION["FRAMEWORK_ROUTE_ERRORS_VARIABLES"] = $errorVariables;

            $this->safeRedirect($redirectBuildTarget);
        };
        return $this;
    }

    public function to($target){

        if(isset($this->_to)){
            ($this->_to)($target);
        }
        return $this;
    }
    public function with($varName,$value){
        if(isset($this->_with)){
            ($this->_with)($varName,$value);
        }
        return $this;
    }

    public function withError($varName,$value){
        if(isset($this->_withError)){
            ($this->_withError)($varName,$value);
        }
        return $this;
    }
    public function execute(){
        if(isset($this->_execute)){
            ($this->_execute)();
        }
    }

    public function buildUrl($target){
        return RouterHelper::sanitizeURL("{$this->selfUrl}/{$target}");
    }
}
