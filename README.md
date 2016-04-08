# Reagieren

[![Codacy Badge](https://api.codacy.com/project/badge/grade/7b18de58d3ad44a28670caae83a9ba42)](https://www.codacy.com/app/italolelis/reagieren)
[![Codacy Badge](https://api.codacy.com/project/badge/coverage/7b18de58d3ad44a28670caae83a9ba42)](https://www.codacy.com/app/italolelis/reagieren)

Reagieren is a set of adapters for many different queues systems.

Currently it's hard to have one unique interface to access RabbitMQ, ApacheKafka, IronMQ, etc...
So why not having only one common interface and implementing adapters that follows this interfaces?
That's the main concept of this library.

### Where can we use this?

Anywhere, that's the beauty of it, you don't depend on a huge framework or any other component. Think about this as
a unified way to access message brokers.

### How to use it?

Check this producer:

```php
use HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp\Producer;

(new Producer('127.0.0.1', 5672, 'guest', 'guest'))->produce('example', 'Hello world this is a message');

echo 'Message sent!', PHP_EOL;
```

Now check this consumer:

```php
use HelloFresh\Reagieren\Message;
use HelloFresh\Reagieren\MessageBroker\RabbitMQ\PHPAmqp\Consumer;

$consumer = new Consumer('127.0.0.1', 5672, 'guest', 'guest');

$callback = function (Message $message) {
    echo 'Received ', $message->getPayload(), PHP_EOL;
};

$consumer->consume(
    'example',
    $callback
);

echo 'Listening...', PHP_EOL;
```

As you can see it's just an adapter that will abstract all the complexity of setting technology specific problems.
For instance to put something on a kafka queue we need to:

1. Set up the consumer
2. Add brokers
3. Create a topic
4. Select the partition
5. Send the payload

But you don't care about partitions, topics, queue balancing, so that's really awesome!

### Producing to multiple brokers

You can produce messages to multiple brokers using `Composite\Producer`, see this example:

```php
use HelloFresh\Reagieren\MessageBroker\Composite\Producer;

//... setup RabbitMQ broker
$rabbitProducer = ...;

//... setup Kafka broker
$kafkaBroker = ...;

$config = [
    'kafka_php' => [
        'topic' => 'example'
    ],
    'rabbit_mq' => [
        'topic' => 'example'
    ]
];

(new Producer($rabbitProducer, $kafkaBroker))->produce($payload, $config);

echo 'Message sent!', PHP_EOL;
```
