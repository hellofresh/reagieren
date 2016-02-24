<?php

namespace HelloFresh\Reagieren\MessageBroker\ApacheKafka\RdKafka;

use HelloFresh\Reagieren\ProducerInterface;
use RdKafka\Producer;

class RdKafkaProducerAdapter implements ProducerInterface
{
    /**
     * @var Producer
     */
    private $producer;

    /**
     * RdKafkaAdapter constructor.
     * @param Producer $producer
     */
    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function produce($topic, $payload, $configs = [])
    {
        $partition = isset($configs['partition']) ? $configs['partition'] : RD_KAFKA_PARTITION_UA;

        $topic = $this->producer->newTopic($topic, isset($configs['topic']) ? $configs['topic'] : null);
        $topic->produce($partition, 0, $payload);
    }
}
