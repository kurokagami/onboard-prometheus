<?php
namespace Framework\Exceptions;
class ActionNotAllowedException extends \Exception {
    public function __construct($controllerName,$actionTarget) {
        parent::__construct("Não foi possivel executar a action {$actionTarget} em {$controllerName}.");
    }
}
