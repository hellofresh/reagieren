<?php

namespace HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp;

use Collections\ArrayList;
use Collections\Dictionary;
use Collections\MapInterface;
use HelloFresh\Reagieren\Message;
use HelloFresh\Reagieren\ConsumerInterface;
use HelloFresh\Reagieren\MessageCollection;
use PhpAmqpLib\Channel\AMQPChannel as Channel;
use PhpAmqpLib\Connection\AMQPStreamConnection as Consumer;
use HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp\PHPAmqpAbstractAdapter as AbstractAdapter;

class PHPAmqpConsumerAdapter extends AbstractAdapter implements ConsumerInterface
{
    /**
    * Default configs
    *
    * @var array
    */
    protected $defaults = [
        'tag'          => '',
        'channel'      => null,
        'passive'      => false,
        'durable'      => false,
        'exclusive'    => false,
        'auto_delete'  => true,
        'nowait'       => false,
        'arguments'    => null,
        'ticket'       => null,
        'no_local'     => false,
        'no_ack'       => false,
        'exclusive'    => false,
        'nowait'       => false,
        'callback'     => null,
        'ticket'       => null,
        'arguments'    => [],
        'force_config' => false,
    ];

    /**
     * PHPAmqpConsumerAdapter Constructor
     *
     * @param $host
     * @param $port
     * @param $username
     * @param $password
     */
    public function __construct($host, $port = 5672, $username = 'guest', $password = 'guest')
    {
        $this->connection = new Consumer($host, $port, $username, $password);
        $this->channels = new ArrayList([
            $this->connection->channel()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function consume($topic, callable $callback, $configs = [])
    {
        if (! $configs instanceof MapInterface) {
            $configs = new Dictionary($configs);
        }

        if (! $this->configured || $configs['force_config']) {
            $configs = $this->setConfig($topic, $configs);
        }

        if (! $choice = $configs->get('channel')) {
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

    private function callback($message, callable $callback)
    {
        return $callback(new Message(
            $message->getBody(),
            $message->get('timestamp'),
            $message->getContentEncoding()
        ));
    }
}
