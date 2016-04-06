<?php

namespace HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp;

use Collections\MapInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Collections\VectorInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection as Producer;

abstract class AbstractAMPQAdapter
{
    /**
     * Default configs
     *
     * @var MapInterface
     */
    protected $configs;

    /**
     * @var Producer
     */
    protected $connection;

    /**
     * @var VectorInterface
     */
    protected $channels;

    /**
     * Prevents reconfiguration
     *
     * @var bool
     */
    protected $configured = false;

    /**
     * PHPAmqpProducerAdapter Destructor
     */
    public function __destruct()
    {
        if ($this->connection instanceof AMQPStreamConnection) {
            $this->connection->close();
        }
    }

    /**
     * Add a new channel
     *
     * @return integer
     */
    public function addChannel()
    {
        $channel = $this->connection->channel();
        $this->channels->add($channel);

        return $channel->getChannelId();
    }

    /**
     * Get the list of open channels
     *
     * @return VectorInterface
     */
    public function getChannels()
    {
        return $this->channels->map(function (AMQPChannel $channel) {
            return $channel->getChannelId();
        });
    }

    /**
     * Configure the producer
     *
     * @param              $topic
     * @param VectorInterface $channels
     * @param MapInterface $configs
     */
    protected function configureChannels($topic, VectorInterface $channels, MapInterface $configs)
    {
        $channels->each(function (AMQPChannel $channel) use ($topic, $configs) {
            $channel->queue_declare(
                $topic,
                $configs->get('passive'),
                $configs->get('durable'),
                $configs->get('exclusive'),
                $configs->get('auto_delete'),
                $configs->get('nowait'),
                $configs->get('arguments'),
                $configs->get('ticket')
            );
        });

        $this->configured = true;
    }
}
