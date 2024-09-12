<?php

namespace App\Framework\Routing;

interface IRouteManagerService{
    public function safeRedirect($target);
    public function redirect($target);
}
