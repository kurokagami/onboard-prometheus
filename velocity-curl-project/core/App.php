<?php
namespace Framework;
use Dotenv\Dotenv;
use Framework\Exceptions\NotFoundHttpException;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class App{

    private $basePath;

    //Serviços
    public  $serviceProvider;
    private $interceptorsManager;
    private $logger;
    private $routerService;

    //Interno
    private $runStatus;


    public function __construct(){
        global $app;
        $app = $this;

        //Carregar variaveis de ambiente
        $this->loadEnv();

        //Configurar dependencias
        $this->initServiceProvider();

        //Inicialização de Interceptor Manager
        $this->interceptorsManager = $this->serviceProvider->get('InterceptorsManager');

        //Recolhimento de Logger
        $this->logger = $this->serviceProvider->get('App\Framework\Logging\ILogging');

        //Inicialização dos processos
        $this->runStatus = false;
    }

    //Função que executa o middleware
    public function run(){

        //Lidar com listener de health
        if($this->isHealthRequest()){
            $this->handleHealthRequest();
            die();
        }
        try{
            //Recuperar navegação do Container
            $this->routerService = $this->serviceProvider->get("Framework\Routing\Contracts\IRouterService");

            //Setar Path se existir
            if($this->basePath){
                $this->routerService->appendBasePath($this->basePath);
            }

            //Executar interceptors globais registrados ate o momento
            $this->handleGlobalInterceptors();

            ob_start();
        
            //Navegar
            $this->call( ($this->routerService)->navigate());
           
        }catch(Exception $e){
            $this->handlerExceptions($e);
            try{
                $this->handleExceptionInterceptors($e);
            }catch(Exception $e){
                $this->handlerExceptions($e);
            }
        }
        ob_end_flush();


        //Mudar estado de execução
        $this->runStatus = true;
    }

    private function handlerExceptions($e){
        $output = ob_get_contents();
        if(empty($output)){
             switch(get_class($e)){
             case "Framework\Exceptions\NotFoundHttpException":
                 $errorCode = 404;
                 break;
                 default:
                 $errorCode = 500;
                 break;
             }

             include_once "./core/resources/exception.php";
            }
    }
    //Função de registro de middleware/interceptors globais
    public function registerInterceptor($interceptor,$data = []){
        $this->interceptorsManager->registerGlobalInterceptor($interceptor,$data,$this->runStatus);
    }

    private function handleGlobalInterceptors(){
        $this->interceptorsManager->handleGlobalInterceptors();
    }
    
    private function handleExceptionInterceptors($e){
        $this->interceptorsManager->handleCatchInterceptors($e);
    }

    private function loadEnv(){
        try{
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
            
        }catch(\Exception $e){
            $this->handlerExceptions($e);
        }
    }

    private function isHealthRequest(){
        $uri =  explode("/",$_SERVER['REQUEST_URI']);
        return end($uri)  === 'health';
    }
 
    private function handleHealthRequest(){
        header('Content-Type: application/json');
        echo json_encode(["status"=>true,"timestamp"=>time()]);
    }

    public function setBasePath($path){
        $this->basePath = $path;
    }

    public function initServiceProvider(){
        try{
            $this->serviceProvider = new ServiceProviderManager();

            $directory = "app".DIRECTORY_SEPARATOR."app_modules";
            $foundServicesCollection = [];

            if (!is_dir($directory)) {
                return;
            }

            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        
            // Percorre todos os arquivos e diretórios
            foreach ($iterator as $arquivo) {
                if ($arquivo->getFilename() === 'ServiceProviderRegister.php') {
                    array_push($foundServicesCollection,$arquivo->getPathname());
                }
            }

            $this->serviceProvider->pushDependencies($foundServicesCollection);

            $this->serviceProvider->init();
        }catch(Exception $e){
            //Caracteres UTF-8
            header('Content-Type: text/html; charset=utf-8');
            handlerExceptions($e);
        }
    
    }

    private function call($t){

        //Recuperar Retorno para Render
        $this->logger->debug("Execução de Action",[
            "controller"=>$t["controller"],
            "action"=>$t["method"],
            "params"=>$t["params"]
        ]);

        call_user_func_array([$t["controller"], $t["method"]],$t["params"]);
        
    }
}
