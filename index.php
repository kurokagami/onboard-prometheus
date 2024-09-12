<?php
//Carregamento do Composer
try{
    require_once 'vendor/autoload.php';
}catch(Exception $e){
    echo "Não foi possivel encontrar o autoloader das classes, refaça o composer install!";
}

//Timezone para Brasil
date_default_timezone_set("America/Sao_Paulo");


$myApp = new Framework\App();

//Customizar basePath
$myApp->setBasePath("");

//Execução da request HTTP
$myApp->run();

