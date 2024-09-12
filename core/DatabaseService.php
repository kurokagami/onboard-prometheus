<?php

namespace App\Framework;

use PDO;

abstract class DatabaseService{

    protected $pdo;

    public function __construct(PDO $pdo){
        //Objeto PDO
        $this->pdo = $pdo;
    }

    public function getById($table,$id,$field = "id"){
        try{
            $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE {$field} = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return [true,$stmt->fetch(PDO::FETCH_ASSOC)];
        }catch(\PDOException $e){
            return [false,$e->message];
        }
    }

    public function getAll($table,$fields = [],$limit = -1){
        try{
            $fieldsStr = empty($fields) ? '*' : implode(', ', $fields);
            $stmt = $this->pdo->prepare("SELECT {$fieldsStr} FROM {$table}");
            $stmt->execute();
            return [true,$stmt->fetchAll(PDO::FETCH_ASSOC)];
        }catch(\PDOException $e){
            return [false,$e->message];
        }
    }
}
