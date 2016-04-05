<?php

namespace HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp;

use Collections\ArrayList;
use Collections\Dictionary;
use Collections\MapInterface;
use HelloFresh\Reagieren\ConsumerInterface;
use HelloFresh\Reagieren\Message;
use HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp\AbstractAMPQAdapter as AbstractAdapter;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer extends AbstractAdapter implements ConsumerInterface
{
    /**
     * Default configs
     *
     * @var array
     */
    private static $defaults = [
        'tag' => '',
        'channel' => null,
        'passive' => false,
        'durable' => false,
        'exclusive' => false,
        'auto_delete' => true,
        'nowait' => false,
        'arguments' => null,
        'ticket' => null,
        'no_local' => false,
        'no_ack' => false,
        'callback' => null,
        'force_config' => false,
    ];

    /**
     * ConsumerAMPQAdapter Constructor
     *
     * @param $host
     * @param $port
     * @param $username
     * @param $password
     */
    public function __construct($host, $port = 5672, $username = 'guest', $password = 'guest')
    {
        $this->configs = new Dictionary(static::$defaults);
        $this->connection = new AMQPStreamConnection($host, $port, $username, $password);
        $this->channels = new ArrayList([
            $this->connection->channel()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function consume($topic, callable $callback, $configs = [])
    {
        if (!$configs instanceof MapInterface) {
            $configs = new Dictionary($configs);
        }

        $this->configs->concat($configs);

        if (!$this->configured || $configs['force_config']) {
            $this->configureChannels($topic, $this->channels, $this->configs);
        }

        if (!$choice = $configs->get('channel')) {
            $choice = $this->connection->get_free_channel_id();
        }

        $channel = $this->connection->channel($choice);

        $channel->basic_consume(
            $topic,
            $configs->get('tag'),
            $configs->get('no_local'),
            $configs->get('no_ack'),
            $configs->get('exclusive'),
            $configs->get('nowait'),
            function ($message) use ($callback) {
                return $this->callback($message, $callback);
            },
            $configs->get('ticket'),
            $configs->get('arguments')
        );

        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }

    /**
     * Callback called in the consumer's callback that takes a callable $callback and runs it as a callback.
     *
     * @param           $message
     * @param  callable $callback
     * @return $callback
     */
    private function callback(AMQPMessage $message, callable $callback)
    {
        return $callback(new Message(
            $message->getBody(),
            \DateTime::createFromFormat("U", $message->get('timestamp')),
            $message->getContentEncoding()
        ));
    }
}
