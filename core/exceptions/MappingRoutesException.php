<?php
namespace Framework\Exceptions;
class MappingRoutesException extends \Exception {
    public function __construct($msg) {
        parent::__construct($msg);
    }
}
