<?php

namespace HelloFresh\Reagieren\MessageBroker\ApacheKafka\KafkaPHP;

use Kafka\Consumer;
use Collections\Dictionary;
use Collections\MapInterface;
use HelloFresh\Reagieren\Message;
use HelloFresh\Reagieren\ConsumerInterface;

class KafkaPHPConsumerAdapter extends KafkaPHPAbstractAdapter implements ConsumerInterface
{
    /**
     * Default configs
     *
     * @var array
     */
    protected $defaults = [
        'group'           => 'default',
        'partition'       => 0,
        'offset'          => 0,
        'max_bytes'       => 1048576,
        'offset_strategy' => -1,
        'stream_options'  => [],
        'force_config'    => false,
    ];

    /**
     * KafkaConsumerAdapter Constructor
     *
     * @param $zookeeperHost
     * @param $zookeeperPort
     */
    public function __construct($zookeeperHost, $zookeeperPort = 2181)
    {
        $this->connection = Consumer::getInstance("$zookeeperHost:$zookeeperPort");
    }

    /**
     * {@inheritdoc}
     */
    public function consume($topic, callable $callback, $configs = [])
    {
        if (! $configs instanceof MapInterface) {
            $configs = new Dictionary($configs);
        }

        if (! $this->configured || $configs->get('force_config')) {
            $configs = $this->setConfig($topic, $configs);
        }

        while (true) {
            $this->fetch($topic, $callback);
        }
    }

    /**
     * Fetch the messages from the queue
     *
     * @param          $topic
     * @param callable $callback
     */
    private function fetch($topic, callable $callback)
    {
        $response = $this->connection->fetch();

        foreach ($response as $t => $partition) {
            if ($topic !== $t) {
                continue;
            }

            foreach ($partition as $messages) {
                foreach ($messages as $message) {
                    $callback(new Message(
                        $message->getMessage(),
                        time(), // TODO: Kafka doesn't give you this information - can we serialise it into the message body?
                        null
                    ));
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setConfig($topic, MapInterface $configs)
    {
        $configs = (new Dictionary($this->defaults))->concat($configs);

        $this->connection->setMaxBytes($configs->get('max_bytes'));
        // $this->connection->setFromOffset($configs->get('offset')); # Disabled until I've figured out offsets in my head
        $this->connection->setOffsetStrategy($configs->get('offset_strategy'));

        parent::setConfig($topic, $configs);
    }
}
