<?php
namespace Framework\Exceptions;
class ControllerCreationException extends \Exception {
    public function __construct($e) {
        parent::__construct('Erro ao refletir sobre a classe: ' . $e);
    }
}
