<?php

namespace App\Factories;
use App\Models\Message;

interface IMessageFactory {
    public function criarMessagemForm($post) : Message;

    public function criarMessagemDb($row) : Message;
}