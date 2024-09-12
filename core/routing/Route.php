<?php
namespace Framework\Routing;

final class Route{

    public $controllerName;
    public $action;
    public $httpMethod;
    public $httpRoute;

    public function __construct($controllerName,$action,$httpMethod,$httpRoute){
        $this->controllerName = $controllerName;
        $this->action = $action;
        $this->httpMethod = $httpMethod;
        $this->httpRoute = $httpRoute;
    }
}
