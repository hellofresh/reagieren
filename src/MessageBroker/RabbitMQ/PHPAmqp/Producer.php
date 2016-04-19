<?php

namespace HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp;

use Collections\ArrayList;
use Collections\Dictionary;
use Collections\MapInterface;
use HelloFresh\Reagieren\ProducerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage as Message;

class Producer extends AbstractAMPQAdapter implements ProducerInterface
{
    protected static $defaults = [
        'tag' => '',
        'channel' => null,
        'passive' => false,
        'durable' => false,
        'exclusive' => false,
        'auto_delete' => true,
        'nowait' => false,
        'arguments' => null,
        'ticket' => null,
        'force_config' => false,
    ];

    /**
     * PHPAmqp Producer Constructor
     *
     * @param $host
     * @param $port
     * @param $username
     * @param $password
     */
    public function __construct($host, $port = 5672, $username = 'guest', $password = 'guest')
    {
        $this->configs = new Dictionary(static::$defaults);

        $this->connection = new AMQPStreamConnection($host, $port, $username, $password);
        $this->channels = new ArrayList([
            $this->connection->channel()
        ]);
    }
    
    /**
     * @return bool
     */
    public function isConnected()
    {
        if (!isset($this->connection)) {
            return false;
        }
        
        return $this->connection->isConnected();
    }    

    /**
     * {@inheritdoc}
     */
    public function produce($topic, $payload, $configs = [])
    {
        if (!$configs instanceof MapInterface) {
            $configs = new Dictionary($configs);
        }

        $this->configs->concat($configs);

        if (!$this->configured || $this->configs->get('force_config')) {
            $this->configureChannels($topic, $this->channels, $this->configs);
        }

        if (!$choice = $this->configs->get('channel')) {
            $choice = $this->connection->get_free_channel_id();
        }

        $now = new \DateTime();
        $this->connection->channel($choice)->basic_publish(
            new Message($payload, ['timestamp' => $now->getTimestamp()]),
            $this->configs->get('tag'),
            $topic
        );
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rabbit_mq';
    }
}
