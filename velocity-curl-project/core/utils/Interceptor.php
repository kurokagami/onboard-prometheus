<?php
namespace Framework\Utils;

abstract class Interceptor{

    protected $routerManagerService;
    protected $renderService;
    
    public function init($router,$render){
        $this->routerManagerService = $router;
        $this->renderService = $render;

    }

    protected function abortAndRedirect($target){
        header("Location: {$target}");
        exit();
    }

    protected function redirect($t){
        if($this->routerManagerService){
            $this->routerManagerService->safeRedirect($t);
        }
    }

    public function response(){
        return $this->renderService;
    }

}
