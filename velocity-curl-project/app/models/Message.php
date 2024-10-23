<?php 

namespace App\Models;


class Message {
    private $id;
    private $name;
    private $email;
    private $message;
    private $phone;
    private $ddd;

    public function getName(){
        return $this->name;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getMessage(){
        return $this->message;
    }

    public function getPhone(){
        return $this->phone;
    }

    public function getDdd(){
        return $this->ddd;
    }

    public function getId(){
        return $this->id;
    }

    public function setName($name){
        $this->name = $name;
    }
    public function setEmail($email){
        $this->email = $email;
    }
    public function setMessage($message){
        $this->message = $message;
    }
    public function setPhone($phone){
        $this->phone = $phone;
    }
    public function setDdd($ddd){
        $this->ddd = $ddd;
    }
    public function setId($id){
        $this->id = $id;
    }

    //função do cURL
    public function getData()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
            'phone' => $this->phone,
            'ddd' => $this->ddd
        ];
    }
}    