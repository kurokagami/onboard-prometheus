<?php
namespace Framework;

use App\Framework\Routes\Interceptor;
use Doctrine\Common\Annotations\AnnotationReader;
use Framework\Interfaces\IServiceProvider;
use Framework\Exceptions\InvalidInterceptorException;
use App\Framework\IRequest;
use DI\Annotation\Scope;

use App\Framework\Logging\ILogging;

/**
 * @Scope("singleton")
 */
final class InterceptorsManager{
    
    private $globalInterceptors = [];
    private $localInterceptors = [];

    private const INTERCEPTORS_NAMESPACE = "App\\Middleware";
    private const EXCEPTIONINTERCEPTOR = "Framework\\Utils\\ExceptionInterceptor";
    private const INTERCEPTOR = "Framework\\Utils\\Interceptor";

    public function __construct(
        private IServiceProvider $serviceProvider,
        private IRequest $request,
        private ILogging $logger
        ){
    }

    public function registerControllerInterceptors($controllerName){
        $annotationReader = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($controllerName);

        $classAnnotations = $annotationReader->getClassAnnotations($reflectionClass);
        $interceptorAnnotations = $this->getOnlyInterceptors($classAnnotations);
        foreach ($interceptorAnnotations as $annotation) {
            $annotation->value = $this->mapModuleName($controllerName,$annotation->value);
            if($targetInterceptor = $this->validateInterceptor($annotation)){
                array_push($this->localInterceptors,[$targetInterceptor,$annotation->vars]);
            }
        }
    }

    private function getOnlyInterceptors($array){
        $result  = [];
        foreach($array as $annotation){
            if($annotation instanceof Interceptor ){
                array_push($result,$annotation);
            }
        }
        return $result;
    }
    
    private function mapModuleName($targetInterceptor,$namespace){
        if(strpos($namespace,"@") === 0 ){
            $parts = explode("\\",$targetInterceptor);
            return str_replace("@",$parts[count($parts)-3].":",$namespace);
        }
        return $namespace;
    }

    public function registerLocalInterceptors($routeInstance){
    
        $annotationReader = new AnnotationReader();
        $reflectionMethod = new \ReflectionMethod($routeInstance->controllerName, $routeInstance->action);
        $actionAnnotations = $annotationReader->getMethodAnnotations($reflectionMethod);
        $interceptorAnnotations = $this->getOnlyInterceptors($actionAnnotations);
        
        foreach ($interceptorAnnotations as $annotation) {
            $annotation->value = $this->mapModuleName($routeInstance->controllerName,$annotation->value);
            if($targetInterceptor = $this->validateInterceptor($annotation)){
                array_push($this->localInterceptors,[$targetInterceptor,$annotation->vars]);
            }
        }
    }

    public function registerGlobalInterceptor($interceptor,$data,$runStatus){

        $targetInterceptor = self::INTERCEPTORS_NAMESPACE. "\\".$interceptor;


        if(class_exists($targetInterceptor)){
            if(!$runStatus){
                array_push($this->globalInterceptors,["before",[$targetInterceptor,$data]]);

            }else{
                array_push($this->globalInterceptors,["after",[$targetInterceptor,$data]]);
                $this->buildHandler(end($this->globalInterceptors)[1],"global","after");
            }
        }else{
            throw new InvalidInterceptorException("Interceptor {$targetInterceptor} não é valido");
        }
    }

    
    //Função que executa todos os interceptadores de rota encontrados e cadastrados corretamente
    public function handleLocalInterceptors(){
        foreach($this->localInterceptors as $interceptor){
            
            $this->buildHandler($interceptor,"local","before");
        }
    }

    private function buildHandler($interceptor,$type,$order){
        $object  = $this->serviceProvider->container->make($interceptor[0]);
        if(is_subclass_of($object, self::INTERCEPTOR)){
            $rm = $this->serviceProvider->container->get("App\Framework\Routing\IRouteManagerService");
            $r = $this->serviceProvider->container->get("Framework\RenderDecorator");
            $object->init($rm,$r);
            $reflectionClass = new \ReflectionClass($object);
            if ($reflectionClass->hasMethod("handle")) {
                $bindings = [];
                $parametersList  =  $reflectionClass->getMethod("handle")->getParameters();
                foreach($parametersList as $parameter){
                    if($parameter->getType() == "App\Framework\Request"){
                        array_push($bindings,$this->request);
                    }elseif(isset($interceptor[1][$parameter->getName()])){
                        array_push($bindings,$interceptor[1][$parameter->getName()]);
                    }else{
                        array_push($bindings,null);
                    }
                }
                
                $this->logger->debug("Chamada de Middleware",[
                    "nome"=>$interceptor[0],
                    "type"=>$type,
                    "order"=>$order
                ]);
                $object->handle(...$bindings);
            }
        }
    }
    public function handleGlobalInterceptors(){
 
        foreach($this->globalInterceptors as $interceptor){
            $this->buildHandler($interceptor[1],"global","before");
        }
    }

    public function handleCatchInterceptors($e){
        foreach($this->globalInterceptors as $interceptor){
            if(
                $interceptor[0] == "catch" && $interceptor[0] &&
                is_subclass_of($interceptor[1], self::EXCEPTIONINTERCEPTOR)
            ){
                ($this->serviceProvider->container->make($interceptor[1]))->catch($e);
            }
        }

        foreach($this->localInterceptors as $interceptor){
            
            $object  = $this->serviceProvider->container->make($interceptor[0]);
            if(is_subclass_of($object, self::EXCEPTIONINTERCEPTOR)){
                $object->catch($e);
            }
        }
    }

    private function validateInterceptor($annotation){
        $interceptor = preg_match("/^app\:\b/", $annotation->value) ?
        substr($annotation->value, 4) :
        $annotation->value;
   
        if(strpos($interceptor,":") !== false){
            $parts=explode(':',  $interceptor, 2);
            $module = $parts[0];
            $interceptor = $parts[1];
            $targetInterceptor = "App\\Modules\\".ucfirst($module)."\\Middleware\\{$interceptor}";
        }else{
            $targetInterceptor = self::INTERCEPTORS_NAMESPACE."\\".$interceptor;
        }
        
        if(
            class_exists($targetInterceptor) &&
            (is_subclass_of($targetInterceptor, self::INTERCEPTOR) ||
            is_subclass_of($targetInterceptor, self::EXCEPTIONINTERCEPTOR))
        ){
            return $targetInterceptor;
        }else{
            throw new InvalidInterceptorException("Interceptor {$targetInterceptor} não é valido");
        }
    }
}
