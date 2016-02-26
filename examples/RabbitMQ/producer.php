<?php

require_once '../vendor/autoload.php';

use HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp\PHPAmqpProducerAdapter as Producer;

(new Producer('127.0.0.1', 5672, 'guest', 'guest'))->produce('example', 'Hello world this is a message');
echo 'Message sent!', PHP_EOL;
