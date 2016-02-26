<?php
require_once '../vendor/autoload.php';

use HelloFresh\Reagieren\Message;
use HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp\PHPAmqpConsumerAdapter as Consumer;

$consumer = new Consumer('127.0.0.1', 5672, 'guest', 'guest');

$callback = function (Message $message) {
    echo 'Received ', $message->body, PHP_EOL;
};

$consumer->consume(
    'example',
    $callback
);

echo 'Listening...', PHP_EOL;
