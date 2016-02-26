<?php

namespace HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp;

use Collections\Dictionary;
use Collections\MapInterface;

class PHPAmqpAbstractAdapter
{
    /**
     * Prevents reconfiguration
     *
     * @var bool
     */
    protected $configured = false;

    /**
     * Configure the producer
     *
     * @param              $topic
     * @param MapInterface $configs
     */
    protected function setConfig($topic, MapInterface $configs)
    {
        $configs = (new Dictionary($this->defaults))->concat($configs);

        $this->channel->queue_declare(
            $topic,
            $configs->get('passive'),
            $configs->get('durable'),
            $configs->get('exclusive'),
            $configs->get('auto_delete'),
            $configs->get('nowait'),
            $configs->get('arguments'),
            $configs->get('ticket')
        );

        $this->configured = true;

        return $configs;
    }
}
