<?php

namespace HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp;

use Collections\ArrayList;
use Collections\Dictionary;
use Collections\MapInterface;
use HelloFresh\Reagieren\ProducerInterface;
use PhpAmqpLib\Channel\AMQPChannel as Channel;
use PhpAmqpLib\Message\AMQPMessage as Message;
use PhpAmqpLib\Connection\AMQPStreamConnection as Producer;

class PHPAmqpProducerAdapter extends PHPAmqpAbstractAdapter implements ProducerInterface
{
    /**
    * Default configs
    *
    * @var array
    */
    protected $defaults = [
        'tag'          => '',
        'channel'      => null,
        'passive'      => false,
        'durable'      => false,
        'exclusive'    => false,
        'auto_delete'  => true,
        'nowait'       => false,
        'arguments'    => null,
        'ticket'       => null,
        'force_config' => false,
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
        $this->channels = new ArrayList([
            $this->connection->channel()
        ]);
    }

    /**
     * PHPAmqpProducerAdapter Destructor
     */
    public function __destruct()
    {
        $this->connection->close();
    }

    /**
     * {@inheritdoc}
     */
    public function produce($topic, $payload, $configs = [])
    {
        if (! $configs instanceof MapInterface) {
            $configs = new Dictionary($configs);
        }

        if (! $this->configured || $configs['force_config']) {
            $configs = $this->setConfig($topic, $configs);
        }

        if (! $choice = $configs->get('channel')) {
            $choice = $this->connection->get_free_channel_id();
        }

        $this->connection->channel($choice)->basic_publish(
            new Message($payload, [ 'timestamp' => time() ]),
            $configs->get('tag'),
            $topic
        );
    }
}
