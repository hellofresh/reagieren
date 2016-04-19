<?php

namespace HelloFresh\Reagieren\MessageBroker\ApacheKafka\RdKafka;

use HelloFresh\Reagieren\ProducerInterface;
use RdKafka\Producer as RdKafkaProducer;

class Producer implements ProducerInterface
{
    /**
     * @var RdKafkaProducer
     */
    private $producer;

    /**
     * RdKafkaAdapter constructor.
     * @param RdKafkaProducer $producer
     */
    public function __construct(RdKafkaProducer $producer)
    {
        $this->producer = $producer;
    }

    public function produce($topic, $payload, $configs = [])
    {
        $partition = isset($configs['partition']) ? $configs['partition'] : RD_KAFKA_PARTITION_UA;
        if (isset($configs['config'])) {
            $topic = $this->producer->newTopic($topic, $configs['config']);
        } else {
            $topic = $this->producer->newTopic($topic);
        }
        $topic->produce($partition, 0, $payload);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rd_kafka';
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected()
    {
        if (!isset($this->producer) || $this->producer->getOutQLen() < 0) {
            return false;
        }

        return true;
    }
}
