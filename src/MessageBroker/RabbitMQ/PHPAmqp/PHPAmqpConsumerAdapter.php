<?php

namespace HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp;

use Collections\Dictionary;
use Collections\MapInterface;
use HelloFresh\Reagieren\ConsumerInterface;
use HelloFresh\Reagieren\MessageCollection;
use PhpAmqpLib\Channel\AMQPChannel as Channel;
use PhpAmqpLib\Message\AMQPMessage as Message;
use PhpAmqpLib\Connection\AMQPStreamConnection as Consumer;

class PHPAmqpConsumerAdapter implements ConsumerInterface
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
        'tag'          => '',
        'passive'      => false,
        'durable'      => false,
        'exclusive'    => false,
        'auto_delete'  => true,
        'nowait'       => false,
        'arguments'    => null,
        'ticket'       => null,
        'no_local'     => false,
        'no_ack'       => false,
        'exclusive'    => false,
        'nowait'       => false,
        'callback'     => null,
        'ticket'       => null,
        'arguments'    => [],
        'force_config' => false,
    ];

    /**
     * PHPAmqpConsumerAdapter Constructor
     *
     * @param $host
     * @param $port
     * @param $username
     * @param $password
     */
    public function __construct($host, $port = 5672, $username = 'guest', $password = 'guest')
    {
        $this->connection = new Consumer($host, $port, $username, $password);
        $this->channel = $this->connection->channel();
    }

    /**
     * {@inheritdoc}
     */
    public function consume($topic, $offset = null, $count = 0, $configs = [])
    {
        if (! $this->configured || $configs['force_config']) {
            $configs = $this->setConfig($topic, new Dictionary($configs), true);
        }

        $this->channel->basic_consume(
            $topic,
            $configs->get('tag'),
            $configs->get('no_local'),
            $configs->get('no_ack'),
            $configs->get('exclusive'),
            $configs->get('nowait'),
            $configs->get('callback'),
            $configs->get('ticket'),
            $configs->get('arguments')
        );

        return $this->channel; // hmmm
    }
}
