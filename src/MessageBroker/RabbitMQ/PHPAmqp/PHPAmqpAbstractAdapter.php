<?php

namespace HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp;

use Collections\Dictionary;
use Collections\MapInterface;

abstract class PHPAmqpAbstractAdapter
{
    /**
     * @var Producer
     */
    protected $connection;

    /**
     * @var ArrayList
     */
    protected $channels;

    /**
     * Prevents reconfiguration
     *
     * @var bool
     */
    protected $configured = false;

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
     * @return ArrayList
     */
    public function getChannels()
    {
        return $this->channels->map(function ($channel) {
            return $channel->getChannelId();
        });
    }

    /**
     * Configure the producer
     *
     * @param              $topic
     * @param MapInterface $configs
     */
    protected function setConfig($topic, MapInterface $configs)
    {
        $configs = (new Dictionary($this->defaults))->concat($configs);

        foreach ($this->channels as $channel) {
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
        }

        $this->configured = true;

        return $configs;
    }
}
