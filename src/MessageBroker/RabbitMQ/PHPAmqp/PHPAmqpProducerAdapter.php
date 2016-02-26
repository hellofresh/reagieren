<?php

namespace HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp;

use PhpAmqpLib\Channel\AMQPChannel as Channel;
use HelloFresh\Reagieren\ProducerInterface;
use PhpAmqpLib\Message\AMQPMessage as Message;
use PhpAmqpLib\Connection\AMQPStreamConnection as Producer;

class PHPAmqpProducerAdapter implements ProducerInterface
{
    /**
     * @var Producer
     */
    private $connection;

    /**
     * @var Channel
     */
    private $channel;

    /**
    * Default configs
    *
    * @var array
    */
    private $defaults = [
        'passive'       => false,
        'durable'       => false,
        'exclusive'     => false,
        'auto_delete'   => true,
        'nowait'        => false,
        'arguments'     => null,
        'ticket'        => null
    ];

    /**
     * PHPAmqpProducerAdapter Constructor
     *
     * @param $host
     * @param $port
     * @param $username
     * @param $password
     */
    public function __construct($host, $port = 5672, $username = 'guest', $password = 'guest')
    {
        $this->connection = new Producer($host, $port, $username, $password);
        $this->channel = $this->connection->channel();
    }

    /**
     * {@inheritdoc}
     */
    public function produce($topic, $payload, $configs = [])
    {
        $this->setConfig($topic, $configs);

        $this->channel->basic_publish(new Message($payload), '', $topic);

        $this->channel->close();
        $this->connection->close();
    }

    /**
     * Configure the producer
     *
     * @param       $topic
     * @param array $configs
     */
    private function setConfig($topic, array $configs)
    {
        $configs = array_merge($this->defaults, $configs);
        $this->channel->queue_declare(
            $topic,
            $configs['passive'],
            $configs['durable'],
            $configs['exclusive'],
            $configs['auto_delete'],
            $configs['nowait'],
            $configs['arguments'],
            $configs['ticket']
        );
    }
}
