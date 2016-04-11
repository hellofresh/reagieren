<?php

namespace HelloFresh\Reagieren\MessageBroker\Composite;

use Collections\ArrayList;
use Collections\Dictionary;
use Collections\MapInterface;
use Collections\VectorInterface;
use HelloFresh\Reagieren\CompositeProducerInterface;
use HelloFresh\Reagieren\ProducerInterface;

class Producer implements CompositeProducerInterface
{
    /**
     * @var VectorInterface
     */
    private $producers;

    /**
     * Composite Producer Constructor
     *
     * @param array $producers
     */
    public function __construct()
    {
        $this->producers = new ArrayList(func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function produce($payload, $configs = [])
    {
        $configs = new Dictionary($configs);
        $this->producers->each(function (ProducerInterface $producer) use ($payload, $configs) {
            /** @var MapInterface $brokerConfigs */
            $brokerConfigs = $configs->get($producer->getName());

            try {
                $topic = $brokerConfigs->get('topic');
            } catch (\OutOfBoundsException $e) {
                throw new \InvalidArgumentException('You should configure the topic/exchange that you want to produce to');
            }

            $producer->produce($topic, $payload, $brokerConfigs);
        });
    }
}
