<?php
namespace Framework\Exceptions;
final class InvalidDatabaseProviderException extends \Exception {
    public function __construct($providerName) {
        parent::__construct("O DatabaseProvider {$providerName} não é valido para a conexão PDO Padrão!");
    }
}
