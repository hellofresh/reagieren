<?php

namespace HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp;

use Collections\Dictionary;
use Collections\MapInterface;
use HelloFresh\Reagieren\ProducerInterface;
use PhpAmqpLib\Channel\AMQPChannel as Channel;
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
        'ticket'        => null,
        'force_config'  => false,
    ];

    /**
     * Prevents reconfiguration
     *
     * @var bool
     */
    private $configured = false;

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
     * PHPAmqpProducerAdapter Destructor
     */
    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }

    /**
     * {@inheritdoc}
     */
    public function produce($topic, $payload, $configs = [])
    {
        if (! $this->configured || $configs['force_config']) {
            $this->setConfig($topic, new Dictionary($configs));
        }

        $this->channel->basic_publish(new Message($payload), '', $topic);
    }

    /**
     * Configure the producer
     *
     * @param              $topic
     * @param MapInterface $configs
     */
    private function setConfig($topic, MapInterface $configs)
    {
        $configs = (new Dictionary($this->defaults))->concat($configs);

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

        $this->configured = true;
    }
}
