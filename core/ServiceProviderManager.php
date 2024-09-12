<?php
namespace Framework;

use Framework\Interfaces\IServiceProvider;
use Framework\RouterService;
use App\Framework\View;

use DI\ContainerBuilder;
use Framework\Exceptions\InvalidDatabaseProviderException;

use User\User;
use PDO;



//Classe de Proxy para uso do PHP-DI (Basicamente uma classe que abstrai parte do problema, e otimiza)
final class ServiceProviderManager implements IServiceProvider{

    public $container;
    private $initialization;
    private $containerBuilder;
    public function __construct() {
        $this->containerBuilder = new ContainerBuilder();
        $this->containerBuilder->useAutowiring(true);
        $this->initialization = false;

    }

    public function init(){
        $this->registerInternalDependencies();
        $this->registerAppDependencies();
        
        $this->container = $this->containerBuilder->build();

        //Autoregistro de singleton do ServiceProviderManager
        $this->container->set('Framework\Interfaces\IServiceProvider',$this);
        
        //Registro do banco de dados default (se existir)
        if(isset($_ENV['USING_DATABASE']) && $_ENV['USING_DATABASE'] == "YES"){
            $this->registerDatabaseObject();
        }

        $this->initialization = true;

    }

    public function get($serviceName) {
        return $this->container->get($serviceName);
    }

    private function registerInternalDependencies(){
        $this->containerBuilder->addDefinitions([
            'RouterService' => \DI\autowire('Framework\RouterService'),
            'InterceptorsManager' => \DI\autowire('Framework\InterceptorsManager'),
            'Framework\Routing\Contracts\IRouterService' => \DI\autowire('Framework\Routing\RouterService'),
            'App\Framework\Routing\IRouteManagerService' => \DI\autowire('Framework\Routing\RouteManagerService'),
            'Framework\Interfaces\IServiceProvider' => \DI\autowire('Framework\ServiceProviderManager'),
            'App\Framework\Session\ISessionManager' => \DI\autowire('Framework\Session\SessionManager'),
            'App\Framework\IRequest' => \DI\autowire('App\Framework\Request'),
            'App\Framework\Logging\ILogging' => \DI\autowire('App\Framework\Logging\MonologLogging'),

            'View' => \DI\create(View::class)->constructor(\DI\get('Render')),
            'Render' => \DI\create(RenderDecorator::class)->constructor(\DI\get("Render")),

            'Render' => \DI\create(Render::class)->constructor(\DI\get('App\Framework\Routing\IRouteManagerService')),
        ]);
    }

    private function registerAppDependencies(){
        $this->containerBuilder->addDefinitions(__DIR__ . '/../app/ServiceProviderRegister.php');
    }

    public function pushDependencies($collection){
        foreach($collection as $item){
            $this->containerBuilder->addDefinitions($item);
        }
    }

    private function registerDatabaseObject(){
        $this->container->set(PDO::class, function () {
            $dbHost = $_ENV["DATABASE_HOST"];
            $dbName = $_ENV["DATABASE_NAME"];
            $dbUser = $_ENV["DATABASE_USERNAME"];
            $dbPass = $_ENV["DATABASE_PASSWORD"];
            $dbProvider = $_ENV["DATABASE_PROVIDER"];
            
            $providerDSN = "none";

            switch($dbProvider){
                case "mysql":
                    $providerDSN = "mysql:host=$dbHost;dbname=$dbName";
                    break;
                case "sqlite":
                    $providerDSN = "";
                    break;
                default:
                    throw new \InvalidDatabaseProviderException($dbProvider);
                    break;
            }
            
            try {
                return new PDO($providerDSN, $dbUser, $dbPass,  array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                  ));
            }catch (\PDOException $e) {
                echo "Erro de conexão: " . $e->getMessage();
                die();
            }
        });
    }
}

