<?php

require_once __DIR__ . '../../../vendor/autoload.php';

date_default_timezone_set('UTC');

use HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp\Producer as Producer;

(new Producer('127.0.0.1', 5672, 'guest', 'guest'))->produce('example', 'Hello world this is a message');
echo 'Message sent!', PHP_EOL;
