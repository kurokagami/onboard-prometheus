<?php

namespace App\Middleware;
use App\Services\MessagesRepository;
use App\Controllers;
use Framework\Utils\Interceptor;
use App\Framework\Request;

class MiddlewareMessages extends Interceptor{

    public function __construct(){

    }

    public function handlePost(Request $request){
        if ((!$request->post("name")) || 
        (!$request->post("email")) || 
        (!$request->post("message")) || 
        (!$request->post("phone")) || 
        (!$request->post("ddd"))){
            $this->redirect("/home"); // redirect ja mata o codigo;
        }
    }
}
