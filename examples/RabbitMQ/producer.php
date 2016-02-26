<?php

require_once '../vendor/autoload.php';

use HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp\PHPAmqpProducerAdapter as Producer;

(new Producer('rabbitmq'))->produce('example', 'Hello world this is a message');
echo 'Message sent!', PHP_EOL;
