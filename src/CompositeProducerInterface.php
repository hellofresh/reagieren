<?php

namespace HelloFresh\Reagieren;

interface CompositeProducerInterface
{
    /**
     * @param  string $payload The payload to be sent
     * @param  array $configs Specific configurations
     * @return mixed
     */
    public function produce($payload, $configs = []);
}
