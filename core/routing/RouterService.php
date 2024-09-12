<?php
namespace Framework\Routing;

use Framework\Utils\HttpStatusCode;
use Doctrine\Common\Annotations\AnnotationReader;
use App\Framework\Routes\Root;
use App\Framework\Routes\Get;
use App\Framework\Routes\Post;
use App\Framework\Routes\NotFound;
use Framework\Interfaces\IServiceProvider;
use Framework\Routing\Contracts\IRouterService;
use App\Framework\Routing\IRouteManagerService;
use Framework\Exceptions\NotFoundHttpException;
use Framework\Exceptions\MappingRoutesException;
use Framework\Exceptions\ControllerCreationException;


use App\Middleware;
use Exception;
use DI\Annotation\Scope;

#[\AllowDynamicProperties]
final class RouterService implements IRouterService{

    const CONTROLLERS_NAMESPACE = "App\\Controllers";
    const REQUEST_BINDING_FLAG = "@request";
    private $availableRoutes;
    private $notFoundAction;
    private $request;

    public function __construct(private IServiceProvider $serviceProvider,private IRouteManagerService $routeManager){
        //Inicialização do mapeamento de rotas
        $this->availableRoutes = [];

        $this->request = $this->serviceProvider->container->get("App\Framework\IRequest");

        //Rota de 404
        $this->notFoundAction = false;

        //Mapeamento de todas as rotas de todas as controllers
        try{
            $this->mapAvailableRoutes();
        }catch(Exception $e){
            throw new MappingRoutesException($e->getMessage());
        }
    }
    
    public function appendBasePath($basePath){
        $this->routeManager->setBaseUrl($basePath);
    }


    //Função raiz de navegação (via HTTP)
    public function navigate(){

        //Remoção de / do fim da URL
        $targetRouteCS = rtrim($_GET["target"], '/');
        $targetRoute = strtolower($targetRouteCS);

        //Metodo HTTP usado nessa request
        $targetMethod = $this->request->getMethod();

        //Chave de busca de rota
        $key= $targetMethod."_".$targetRoute;

        //Se achou uma rota compativel (rota/metodo) então
        if($foundKey = RouterHelper::canNavigate($this->availableRoutes,$key)){
         
            //Criar controller usando PHP-DI
            //$targetController = new $this->availableRoutes[$foundKey]->controllerName();
            $targetControllerName = $this->availableRoutes[$foundKey]->controllerName;
            $targetController = $this->serviceProvider->container->make($targetControllerName);
            
            //Inicializar Controller
            $targetController->initController($this->serviceProvider);

            //Recuperar metodo (ação)
            $targetMethod = $this->availableRoutes[$foundKey]->action;


            //Mapear variaveis de binding da URL
            preg_match_all('/\{:(.*?)\}/', $this->availableRoutes[$foundKey]->httpRoute, $matches);

            //Recuperar values de Binding
            $values =  $this->retrieveBindingData($targetRouteCS,$foundKey);
            $reflectionMethod = new \ReflectionMethod($targetController, $targetMethod);
            $parameters = $reflectionMethod->getParameters();


            //Se Todos os parametros foram encontrados no mapeamento
            if (is_array($params = $this->handleBindings($parameters,$values))){
                

                //Verifica a existencia de interceptors na Controller
                $this->manageControllerInterceptors($targetControllerName);
               
                //Verifica a existencia de interceptors na Action
                $this->manageActionInterceptors($this->availableRoutes[$foundKey]);
                
                //Executa todos os interceptors encontrados
                $this->handleRouteInterceptorsChain();
                
                //Recupera as mensagens em caso de existencia
                $messagesContainer = [
                    "messages"=>$_SESSION["FRAMEWORK_ROUTE_VARIABLES"] ?? null,
                    "errors"=> $_SESSION["FRAMEWORK_ROUTE_ERRORS_VARIABLES"] ?? null
                ];
                
                //Remover variaveis da memoria após copia
                unset($_SESSION["FRAMEWORK_ROUTE_VARIABLES"]);
                unset($_SESSION["FRAMEWORK_ROUTE_ERRORS_VARIABLES"]);

                //Receber mensagens em caso de existencia da rota
                $targetController->recieveMessages($messagesContainer);
                return [
                    "controller"=>$targetController,
                    "method"=> $targetMethod,
                    "params"=>$params
                ];
            } else {
                throw new \Error("Erro interno, bindings não foram preenchidos.");
            }
        }else{
            return $this->handleNotFound($targetRoute);
        }
    }

    private function handleBindings($parameters,$values){
        $expectedBindings = [];
        foreach ($parameters as $parameter) {
            if($parameter->getType() == "App\Framework\Request"){
                $expectedBindings[] = self::REQUEST_BINDING_FLAG;
                continue;
            }
            $expectedBindings[] = strtolower($parameter->getName());
        }

        // Verificar se todos os expectedBindings estão preenchidos em values
        $allBindingsFilled = true;
        foreach ($expectedBindings as $binding) {
            if (!array_key_exists($binding, $values) && $binding != self::REQUEST_BINDING_FLAG ) {
                $allBindingsFilled = false;
                break;
            }
        }
        //Montar parametros para array na ordem requerida
        $params = [];
        foreach($expectedBindings as $binding){
            if($binding == self::REQUEST_BINDING_FLAG ){
                array_push($params, $this->request);
            }else{
                array_push($params, $values[$binding]);
            }
        }
        return $allBindingsFilled ? $params : false;
    }

    //Lidar com caso de 404
    private function handleNotFound($targetRoute){
      
        //Exibir exception caso a rota de notFound não tiver definida
        if(!$this->notFoundAction){
            throw new NotFoundHttpException("Rota buscada não encontrada: {$targetRoute}");
        }
        $notFoundController = $this->serviceProvider->container->make($this->notFoundAction->controllerName);
        $notFoundController->initController($this->serviceProvider);

        $notFoundAction = $this->notFoundAction->action;
        //Chamar Action de Not Found
        return [
            "controller"=>$notFoundController,
            "method"=> $notFoundAction,
            "params"=>[]
        ];
    }


    private function mapAvailableRoutes() {
        $appPath = "app" . DIRECTORY_SEPARATOR;
        $controllersDirectory = "controllers";
        $modulesDirectory = "app_modules";
        $slash = DIRECTORY_SEPARATOR;
        
        // Verifica e itera sobre os controllers globais
        $globalControllerPath = $appPath . $controllersDirectory;
        if (is_dir($globalControllerPath)) {
            $globalFiles = array_diff(scandir($globalControllerPath), ['.', '..']);
            foreach ($globalFiles as $file) {
                $filePath = "{$globalControllerPath}{$slash}{$file}";
                $this->processControllerFile($filePath);
            }
        }
    
        // Verifica e itera sobre os controllers dos módulos
        $modulesPath = $appPath . $modulesDirectory;
        if (is_dir($modulesPath)) {
            $modules = array_diff(scandir($modulesPath), ['.', '..']);
            foreach ($modules as $module) {
                $moduleControllerPath = "{$modulesPath}{$slash}{$module}{$slash}{$controllersDirectory}";
                if (is_dir($moduleControllerPath)) {
                    $moduleFiles = array_diff(scandir($moduleControllerPath), ['.', '..']);
                    foreach ($moduleFiles as $file) {
                        $filePath = "{$moduleControllerPath}{$slash}{$file}";
                        $this->processControllerFile($filePath);
                    }
                }
            }
        }
    }
    
    private function processControllerFile($filePath) {
        $annotationReader = new AnnotationReader();
        try {
            if (!is_dir($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
                $namespace = RouterHelper::getClassNamespace($filePath);
                if ($namespace) {
                    $className = pathinfo($filePath, PATHINFO_FILENAME);
                    $fullClassName = "{$namespace}\\{$className}";
    
                    $reflectionClass = new \ReflectionClass($fullClassName);
    
                    $rootRoute = RouterHelper::getRootRoute($reflectionClass, $annotationReader);
                    $this->getActionsRoutes($rootRoute, $annotationReader, $reflectionClass);
                    $this->getNotFoundAction($annotationReader, $reflectionClass);
                }
            }
        } catch (\ReflectionException $e) {
            // Lidar com erros de reflexão, se necessário
            throw new ControllerCreationException($e->getMessage());
        }
    }
    
    
    //Função que recupera os valores de Binding Dinamico da URL
    private function retrieveBindingData($targetRoute,$foundKey){
        $values = [];

        $urlParts = explode('/', $targetRoute);
        $routeParts = explode('/', trim($this->availableRoutes[$foundKey]->httpRoute,"/"));
        foreach ($routeParts as $index => $part){
            if (strpos($part, '{:') === 0 && isset($urlParts[$index])) {
                
                $binding = trim($part, '{:}');
                $values[strtolower($binding)] = $urlParts[$index];
            }
        }

        return $values;
    }


    private function getNotFoundAction($annotationReader,$controller){
        foreach ($controller->getMethods() as $method) {
            $methodAnnotations = $annotationReader->getMethodAnnotations($method);

            // Loop através das anotações do método
            foreach ($methodAnnotations as $annotation){
                //Verificar existencia de 404
                if(RouterHelper::isNotFoundAction($annotation)){
                    if(!$this->notFoundAction){
                        
                        $this->notFoundAction = new Route(
                            $controller->getName(),
                            $method->getName(),
                            "GET",
                            null);
                    }else{
                        throw new \Error("Já existe uma action de NotFound declarada!");
                    }
                }
            }
        }
    }

    //Função que recupera as rotas de Ação das Funções da Controller
    private function getActionsRoutes($rootRoute,$annotationReader,$controller){
        foreach ($controller->getMethods() as $method) {
            $methodAnnotations = $annotationReader->getMethodAnnotations($method);

            // Loop através das anotações do método
            foreach ($methodAnnotations as $annotation){
                if (RouterHelper::isHttpMethod($annotation)) {
                    $annotationValue = $annotation->value;

                    $httpMethod =  strtoupper(basename(get_class($annotation)));

                    //HOTFIX para LINUX
                    $httpMethodSanitized = explode("\\",$httpMethod);
                    $httpMethod = end($httpMethodSanitized);

                    $finalRoute = RouterHelper::sanitizeURL($rootRoute . $annotationValue);
                    $finalRoute = preg_replace('/\/+$/', '', $finalRoute);

                    //routeKey do Array
                    $routeKey = $httpMethod . '_' .preg_replace('/^\//', '', $finalRoute);
                    
                    $routeKey = preg_replace('/\{:[^\}]*+\}/', '?', $routeKey);

                    
                    if (RouterHelper::isRouteDuplicated($routeKey, $this->availableRoutes)) {
                        throw new \Error("Duplicidade. Ja existe rota declarada em uma das controllers !");
                    } else {
                        $this->availableRoutes[$routeKey] = new Route(
                            $controller->getName(),
                            $method->getName(),
                            $httpMethod,
                            $finalRoute
                        );
                        
                    }
                }
            }
            //Salvar copia para serviço de rota
            $this->routeManager->setRoutesCollection($this->availableRoutes);
        }
    }


    //Função que verifica a existencia de interceptadores globais da Controller
    private function manageControllerInterceptors($controllerName){
        $interceptorsManager = $this->serviceProvider->container->get('InterceptorsManager');
        $interceptorsManager->registerControllerInterceptors($controllerName);
    }

    //Função que verifica a existencia de interceptadores locais da rota e manipula sua execução
    private function manageActionInterceptors($routeInstance){
        $interceptorsManager = $this->serviceProvider->container->get('InterceptorsManager');
        $interceptorsManager->registerLocalInterceptors($routeInstance);
    }

    private function handleRouteInterceptorsChain(){
        $interceptorsManager = $this->serviceProvider->container->get('InterceptorsManager');
        $interceptorsManager->handleLocalInterceptors();
    }
}
