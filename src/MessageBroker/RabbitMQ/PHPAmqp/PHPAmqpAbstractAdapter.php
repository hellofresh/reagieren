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
            $configs['passive'],
            $configs['durable'],
            $configs['exclusive'],
            $configs['auto_delete'],
            $configs['nowait'],
            $configs['arguments'],
            $configs['ticket']
        );

        $this->configured = true;

        return $configs;
    }
}
