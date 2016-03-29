<?php

use HelloFresh\Reagieren\Message;

class MessageTest extends PHPUnit_Framework_TestCase
{
    public function testMessageTimestampIsDateObject()
    {
        $date = new \DateTime();
        $message = new Message('something', $date, 'utf-8');

        $this->assertInstanceOf(\DateTime::class, $message->getTimestamp());
    }

    public function testCastingMessageToStringReturnsPayload()
    {
        $date = new \DateTime();
        $message = new Message('something', $date, 'utf-8');

        $this->assertEquals('something', (string) $message);
    }
}
