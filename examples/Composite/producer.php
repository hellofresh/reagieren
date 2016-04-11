<?php

require_once __DIR__ . '../../vendor/autoload.php';

date_default_timezone_set('UTC');

use HelloFresh\Reagieren\MessageBroker\ApacheKafka\KafkaPHP\Producer as KafkaProducer;
use HelloFresh\Reagieren\MessageBroker\Composite\Producer as CompositeProducer;
use HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp\Producer as RabbitProducer;

function setupRabbitMq()
{
    return new RabbitProducer('rabbitmq');
}

function setupKafka()
{
    return new KafkaProducer('zookeeper');
}

function main()
{
    $rabbitProducer = setupRabbitMq();
    $kafkaProducer = setupKafka();

    $configs = [
        'kafka_php' => [
            'topic' => 'example_topic'
        ],
        'rabbit_mq' => [
            'topic' => 'example_exchange'
        ]
    ];

    $compositeProducer = new CompositeProducer($rabbitProducer, $kafkaProducer);
    $compositeProducer->produce('This is a message!', $configs);

    echo 'Message sent!', PHP_EOL;
}

main();