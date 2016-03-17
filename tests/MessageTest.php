<?php

use HelloFresh\Reagieren\Message;

class MessageTest extends PHPUnit_Framework_TestCase
{
    public function testMessageTimestampIsDateObject()
    {
        $message = new Message('something', 'now', 'utf-8');

        $this->assertInstanceOf(\DateTimeImmutable::class, $message->getTimestamp());
    }

    public function testCastingMessageToStringReturnsPayload()
    {
        $message = new Message('something', 'now', 'utf-8');

        $this->assertEquals('something', (string) $message);
    }
}
