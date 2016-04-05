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
     * @param \DateTime $timestamp
     * @param string $encoding
     */
    public function __construct($payload, \DateTime $timestamp, $encoding)
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
     * @return \DateTime
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
