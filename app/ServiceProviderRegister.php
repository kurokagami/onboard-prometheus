<?php

//Container de Dependencias
return [
    "App\Contracts\IMessagesService" => \DI\Autowire('App\Services\MessagesRepository'),
    "App\Factories\IMessageFactory" => \DI\Autowire('App\Factories\MessageFactory')
];