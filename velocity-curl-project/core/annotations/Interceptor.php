<?php
namespace App\Framework\Routes;

/**
 * @Annotation
 */
final class Interceptor {
    public $value;
    public $vars;
    public function __construct($values){
        $this->vars = [];

        if (isset($values['value'])) {
            $this->value = $values['value'];
            unset( $values['value']);
        }
        $this->vars = $values;
    }
}

