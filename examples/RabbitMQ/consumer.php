<?php
require_once __DIR__ . '../../../vendor/autoload.php';

date_default_timezone_set('UTC');

use HelloFresh\Reagieren\Message;
use HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp\ConsumerAMPQAdapter as Consumer;

$consumer = new Consumer('127.0.0.1', 5672, 'guest', 'guest');

$callback = function (Message $message) {
    echo 'Received ', $message->getPayload(), PHP_EOL;
};

$consumer->consume(
    'example',
    $callback
);

echo 'Listening...', PHP_EOL;
