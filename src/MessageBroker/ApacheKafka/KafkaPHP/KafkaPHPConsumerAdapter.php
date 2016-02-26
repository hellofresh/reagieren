<?php

namespace HelloFresh\Reagieren\MessageBroker\ApacheKafka\KafkaPHP;

use Kafka\Consumer;
use HelloFresh\Reagieren\Message;
use HelloFresh\Reagieren\ConsumerInterface;
use HelloFresh\Reagieren\MessageCollection;

/**
 * Class KafkaPHPConsumerAdapter
 * @package HelloFresh\Reagieren\MessageBroker\ApacheKafka\KafkaPHP
 */
class KafkaPHPConsumerAdapter implements ConsumerInterface
{
    /**
     * @var Consumer
     */
    private $consumer;

    /**
     * @var string
     */
    private $topic;

    /**
     * @var int|null
     */
    private $offset;

    /**
     * Default group to try if none specified
     */
    const DEFAULT_GROUP = 'reagieren';

    /**
     * Default offset to try if none specified
     */
    const DEFAULT_OFFSET = 0;

    /**
     * Default partition to try if none specified
     */
    const DEFAULT_PARTITION = 'reagieren';


    /**
     * KafkaPHPConsumerAdapter constructor.
     * @param     $zookeeperHost
     * @param int $zookeeperPort
     * @param int $zookeeperTimeout
     */
    public function __construct($zookeeperHost, $zookeeperPort = 2181, $zookeeperTimeout = 3000)
    {
        $this->consumer = Consumer::getInstance("$zookeeperHost:$zookeeperPort", $zookeeperTimeout);
    }

    /**
     * {@inheritdoc}
     */
    public function consume($topic, $offset = null, $count = 0, $configs = [])
    {
        if (null === $this->offset) {
            $this->offset = null === $offset ? self::DEFAULT_OFFSET : $offset;
        }

        $group     = isset($configs['group']) ? $configs['group'] : self::DEFAULT_GROUP;
        $partition = isset($configs['partition']) ? $configs['partition'] : self::DEFAULT_PARTITION;

        $this->consumer->setGroup($group);
        $this->consumer->setPartition($topic, $partition, $this->offset);
        $this->topic = $topic;
        $this->consumer->setTopic($topic, $this->offset);

        $messages = $this->fetch();

        $this->offset += $messages->count();

        return $messages;
    }

    /**
     * Fetch the new messages from the queue
     *
     * @return MessageCollection
     */
    private function fetch()
    {
        $response = $this->consumer->fetch();

        $messages = new MessageCollection;

        foreach ($response as $topic => $partition) {
            if ($topic !== $this->topic) {
                continue;
            }

            foreach ($partition as $messageSet) {
                foreach ($messageSet as $message) {
                    // yield new Message($message);
                    $messages->add(new Message($message->getMessage()));
                }
            }
        }

        return $messages;
    }
}
