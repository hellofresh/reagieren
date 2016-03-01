<?php

namespace HelloFresh\Reagieren\MessageBroker\ApacheKafka\KafkaPHP;

use Collections\Dictionary;
use Collections\MapInterface;

abstract class KafkaPHPAbstractAdapter
{
    /**
     * @var
     */
    protected $connection;

    /**
     * Prevents reconfiguration
     *
     * @var bool
     */
    protected $configured = false;

    protected function setConfig($topic, MapInterface $configs)
    {
        $configs = (new Dictionary($this->defaults))->concat($configs);

        $this->connection->setGroup($configs->get('group'));
        $this->connection->setPartition($topic, $configs->get('partition'), $configs->get('offset'));
        $this->connection->setTopic($topic, $configs->get('offset'));
        $this->connection->setStreamOptions($configs->get('stream_options')->toArray());

        $this->configured = true;

        return $configs;
    }
}
