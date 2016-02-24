# Reagieren

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
use HelloFresh\Reagieren\MessageBroker\ApacheKafka\RdKafka\RdKafkaProducerAdapter;

$producer = new RdKafka\Producer();
$producer->addBrokers("127.0.0.1");

$queue = new RdKafkaProducerAdapter($producer);
$queue->produce('test', 'message!!');
```

Now check this consumer:

```php
use HelloFresh\Reagieren\MessageBroker\ApacheKafka\RdKafka\RdKafkaConsumerAdapter;

$consumer = new RdKafka\Consumer();
$consumer->addBrokers("127.0.0.1");

$queue = new RdKafkaConsumerAdapter($consumer);

while (true) {
    $message = $queue->consume('test');
    echo $message->getPayload();
}
```

As you can see it's just an adapter that will abstract all the complexity of setting technology specific problems.
For instance to put something on a kafka queue we need to:

1. Set up the consumer
2. Add brokers
3. Create a topic
4. Select the partition
5. Send the payload

But you don't care about partitions, topics, queue balancing, so that's really awesome!
