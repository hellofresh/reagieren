<?php

namespace HelloFresh\Reagieren;

interface ProducerInterface
{
    /**
     * @param string $topic The name of the topic you will produce the message
     * @param string $payload The payload to be send
     * @param array $configs Specific configurations
     * @return void
     */
    public function produce($topic, $payload, $configs = []);
}
