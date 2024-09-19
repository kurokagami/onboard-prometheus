<?php

namespace App\Factories;
use App\Models\Message;

class MessageFactory implements IMessageFactory {
    public function criarMessagemForm($post) : Message{
        $messageObj = new Message();
        $messageObj->setName($post["name"]);
        $messageObj->setEmail($post["email"]);
        $messageObj->setMessage($post["message"]);
        $messageObj->setPhone($post["phone"]);
        $messageObj->setDdd($post["ddd"]);
        return $messageObj;
    }

    public function criarMessagemDb($row) : Message {
        $messageObj = new Message();
        $messageObj->setId($row["id"]);
        $messageObj->setName($row["name"]);
        $messageObj->setEmail($row["email"]);
        $messageObj->setMessage($row["message"]);
        $messageObj->setPhone($row["phone"]);
        $messageObj->setDdd($row["ddd"]);
        return $messageObj;
    }

}