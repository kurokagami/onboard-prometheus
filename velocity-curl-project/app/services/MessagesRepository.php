<?php

namespace App\Services;

use App\Framework\DatabaseService;
use App\Contracts\IMessagesService;
use App\Framework\Logging\ILogging;
use PDO;

class MessagesRepository extends DatabaseService implements IMessagesService
{
    public function __construct(private ILogging $logService, PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function getMessages(): array
    {
        try {
            $sql = "SELECT * FROM contacts LIMIT 100";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            return $result;
        } catch (\PDOException $e) {
            $this->logService->critical(
                "Erro de Banco de Dados",
                [
                    "class" => "MessagesRepository",
                    "function" => "getMessages",
                    "msg" => $e->getMessage()
                ]
            );
            return [];
        }
    }
    public function saveMessage($messageObj): bool
    {
        try {
                    // Query de inserção
                    $sql = "INSERT INTO contacts (name, email, message, phone, ddd) VALUES (:name, :email, :message, :phone, :ddd)";
    
                    // Preparar a query
                    $stmt = $this->pdo->prepare($sql);
    
                    // Executar com parâmetros
                    $stmt->execute([
                        ':name' => $messageObj->getName(),
                        ':email' => $messageObj->getEmail(),
                        ':message' => $messageObj->getMessage(),
                        ':phone' => $messageObj->getPhone(),
                        ':ddd' => $messageObj->getDdd()
                    ]);
                return true;
            
        } catch (\PDOException $e) {
            $this->logService->critical(
                "Erro de Banco de Dados",
                [
                    "class" => "MessagesRepository",
                    "function" => "postMessages",
                    "msg" => $e->getMessage()
                ]
            );
            return false;
        }
    
    }
}
