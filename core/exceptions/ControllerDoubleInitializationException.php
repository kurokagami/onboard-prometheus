<?php
namespace Framework\Exceptions;
class ControllerDoubleInitializationException extends \Exception {
    public function __construct() {
        parent::__construct("Controller já inicializada, ação não permitida");
    }
}
