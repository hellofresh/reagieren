<?php

namespace HelloFresh\Reagieren;

final class Message
{
    /**
     * @var string
     */
    private $payload;

    /**
     * Message constructor.
     * @param string $payload
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->payload;
    }
}
