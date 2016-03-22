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
     * @param string $iso8601
     * @param string $encoding
     */
    public function __construct($payload, $iso8601, $encoding)
    {
        $this->payload = $payload;
        $this->timestamp = new \DateTimeImmutable($iso8601);
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
     * @return DateTimeImmutable
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
