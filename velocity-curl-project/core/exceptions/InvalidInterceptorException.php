<?php
namespace Framework\Exceptions;
use Framework\Utils\HttpStatusCode;

class InvalidInterceptorException extends \Exception{
    public function __construct($message = 'Not Found')
    {
        parent::__construct($message);
    }
}
