<?php
namespace Framework\Exceptions;
class TemplateNotFoundException extends \Exception {
    public function __construct($templatePath) {
        parent::__construct("Template de view: {$templatePath} não encontrado");
    }
}
