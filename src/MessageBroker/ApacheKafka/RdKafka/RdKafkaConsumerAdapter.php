<?php

namespace HelloFresh\Reagieren\MessageBroker\ApacheKafka\RdKafka;

use HelloFresh\Reagieren\ConsumerInterface;
use HelloFresh\Reagieren\Exception\ConsumerException;
use HelloFresh\Reagieren\Message;
use RdKafka\Consumer;
use RdKafka\ConsumerTopic;
use RdKafka\Queue;

class RdKafkaConsumerAdapter implements ConsumerInterface
{
    /**
     * @var Consumer
     */
    private $consumer;

    /**
     * @var bool
     */
    private $isStarted;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * RdKafkaAdapter constructor.
     * @param Consumer $consumer
     */
    public function __construct(Consumer $consumer)
    {
        $this->consumer = $consumer;
    }

    /**
     * @param $topic
     * @param null $offset
     * @param int $count
     * @param array $configs
     * @return Message
     */
    public function consume($topic, $offset = null, $count = 0, $configs = [])
    {
        $offset = (null !== $offset) ? $offset : RD_KAFKA_OFFSET_BEGINNING;
        $partition = isset($configs['partition']) ? $configs['partition'] : RD_KAFKA_PARTITION_UA;

        /** @var ConsumerTopic $topic */
        $topic = $this->consumer->newTopic($topic, isset($configs['topic']) ? $configs['topic'] : null);
        $this->start($topic, $partition, $offset);

        $message = $this->getQueue()->consume($partition, $count);

        if ($message->err) {
            throw new ConsumerException($message->errstr());
        }

        return new Message(
              $message->payload,
              \DateTime::createFromFormat("U", time()), // TODO: Kafka doesn't give you this information - can we serialise it into the message body?
              null
        );
    }

    /**
     * @param ConsumerTopic $topic
     * @param $partition
     * @param $offset
     */
    private function start(ConsumerTopic $topic, $partition, $offset)
    {
        if (!$this->isStarted) {
            $topic->consumeQueueStart($partition, $offset, $this->getQueue());
        }
    }

    /**
     * @return Queue
     */
    private function getQueue()
    {
        if (null === $this->queue) {
            $this->queue = $this->consumer->newQueue();
        }

        return $this->queue;
    }
}
