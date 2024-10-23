<?php
namespace Framework\Exceptions;
use Framework\Utils\HttpStatusCode;

class NotFoundHttpException extends \Exception{
    public function __construct($message = 'Not Found', $code = 404, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        http_response_code(HttpStatusCode::NOT_FOUND);
    }
}
