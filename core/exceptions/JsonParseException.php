<?php
namespace Framework\Exceptions;
class JsonParseException extends \Exception {
    public function __construct($textPayload) {
        parent::__construct("Input JSON no Payload é invalido: {$textPayload}");
    }
}
