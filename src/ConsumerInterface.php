<?php

namespace HelloFresh\Reagieren;

interface ConsumerInterface
{
    /**
     * @param string $topic The topic from where you want to consume
     * @param null $offset Starting point
     * @param int $count The number of messages to be consumed at once
     * @param array $configs Specific configurations
     * @return Message
     */
    public function consume($topic, $offset = null, $count = 0, $configs = []);
}
