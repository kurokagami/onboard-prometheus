<?php 

namespace App\Contracts;


interface IMessagesService{

    public function getMessages() : array;

    public function saveMessage($messageObj) : bool;
        
}

?>