<?php

namespace HelloFresh\Reagieren;

interface ConsumerInterface
{
    /**
     * @param  string   $topic    The topic / channel to listen for messages on
     * @param  callable $callback The callback to run when a message is received
     * @param  array    $configs  Specific configurations
     * @return void
     */
    public function consume($topic, callable $callback, $configs = []);
}
