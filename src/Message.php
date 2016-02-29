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
    public function __construct($payload, $timestamp, $encoding)
    {
        $this->payload = $payload;
        $this->timestamp = $timestamp;
        $this->encoding = $encoding;
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->payload;
    }
}
