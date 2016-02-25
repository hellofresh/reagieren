<?php

namespace HelloFresh\Reagieren\MessageBroker\ApacheKafka\KafkaPHP;

use Kafka\Produce;
use HelloFresh\Reagieren\ProducerInterface;
use HelloFresh\Reagieren\Exception\ProducerException;

class KafkaPHPProducerAdapter implements ProducerInterface
{
    /**
     * @var Producer
     */
    private $producer;

    const PARTITION_STRATEGY_RANDOM = 'random';

    /**
     * KafkaPHPProducerAdapter constructor
     *
     * @param     $zookeeperHost
     * @param int $zookeeperPort
     * @param int $zookeeperTimeout
     */
    public function __construct($zookeeperHost, $zookeeperPort = 2181, $zookeeperTimeout = 3000)
    {
        $this->producer = Produce::getInstance("$zookeeperHost:$zookeeperPort", $zookeeperTimeout);
    }

    /**
     * {@inheritdoc}
     */
    public function produce($topic, $payload, $configs = [])
    {
        $partition = $this->choosePartition(
            $this->producer->getAvailablePartitions($topic),
            $topic,
            isset($configs['partition_strategy']) ? $configs['partition_strategy'] : self::PARTITION_STRATEGY_RANDOM
        );

        if (is_string($payload)) {
            $payload = [$payload];
        }

        foreach ($payload as $message) {
            $this->producer->setMessages($topic, $partition, $message);
        }

        return $this->producer->send();
    }

    /**
     * Temporary placeholder method for partition choice strategy
     *
     * @param  array $partitions
     * @return int
     */
    private function partitionStrategyRandom(array $partitions)
    {
        return $partitions[array_rand($partitions)];
    }

    /**
     * Using the provided strategy, choose which partition to post messages onto
     *
     * @param  $topic
     * @param  $strategy
     * @throws ProducerException
     * @return mixed
     */
    private function choosePartition($partitions, $topic, $strategy)
    {
        $name = 'partitionStrategy' . ucfirst($strategy);

        if (count($partitions) === 0) {
            throw new ProducerException(sprintf(
                'The provided topic `%s` does not exist. Please create it on the Kafka instance.',
                $topic
            ));
        }

        return $this->$name($partitions);
    }
}
