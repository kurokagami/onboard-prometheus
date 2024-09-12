<?php
namespace Framework\Utils;

abstract class ExceptionInterceptor{

    //Função para ser sobrescrita
    public function catch($exception){
        throw new Exception ("Middleware/Interceptor não implementado!");
    }


}
