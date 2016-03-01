<?php

namespace HelloFresh\Reagieren\MessageBroker\ApacheKafka\KafkaPHP;

use Kafka\Produce;
use Collections\Dictionary;
use Collections\MapInterface;
use HelloFresh\Reagieren\ProducerInterface;
use HelloFresh\Reagieren\Exception\ProducerException;

class KafkaPHPProducerAdapter extends KafkaPHPAbstractAdapter implements ProducerInterface
{
    private $defaults = [
        'require_ack'    => 0,
        'timeout'        => 100,
        'stream_options' => [],
        'force_config'   => false,
    ];

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
        $this->connection = Produce::getInstance("$zookeeperHost:$zookeeperPort", $zookeeperTimeout);
    }

    /**
     * {@inheritdoc}
     */
    public function produce($topic, $payload, $configs = [])
    {
        if (! $configs instanceof MapInterface) {
            $configs = new Dictionary($configs);
        }

        if (! $this->configured || $configs->get('force_config')) {
            $configs = $this->setConfig($topic, $configs);
        }

        $partition = $this->choosePartition(
            $topic,
            isset($configs['partition_strategy']) ? $configs['partition_strategy'] : self::PARTITION_STRATEGY_RANDOM
        );

        if (is_string($payload)) {
            $payload = [$payload];
        }

        foreach ($payload as $message) {
            $this->connection->setMessages($topic, $partition, $message);
        }

        return $this->connection->send();
    }

    protected function setConfig($topic, MapInterface $configs)
    {
        $configs = (new Dictionary($this->defaults))->concat($configs);

        $this->connection->setRequireAck($configs->get('require_ack'));
        $this->connection->setTimeout($configs->get('timeout'));
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
    private function choosePartition($topic, $strategy)
    {
        $name = 'partitionStrategy' . ucfirst($strategy);
        $partitions = $this->connection->getAvailablePartitions($topic);

        if (count($partitions) === 0) {
            throw new ProducerException(sprintf(
                'The provided topic `%s` does not exist. Please create it on the Kafka instance.',
                $topic
            ));
        }

        return $this->$name($partitions);
    }
}
