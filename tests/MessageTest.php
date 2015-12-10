<?php

namespace Tests\LitGroup\Sms;

use LitGroup\Sms\Message;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function getTestsData()
    {
        return [
            /* body, recipients, sender, length */
            ['Hello!', ['79991234567890', '76669999999'], null, 6],
            ['Hello!', ['79991234567890', '76669999999'], 'LitGroup', 6],
            ['Привет, друг!', ['79991234567890', '76669999999'], null, 13],
        ];
    }

    /**
     * @dataProvider getTestsData
     */
    public function testGetters($body, array $recipients, $sender, $length)
    {
        $message = new Message($body, $recipients, $sender);

        $this->assertSame($body, $message->getBody());
        $this->assertSame($recipients, $message->getRecipients());
        $this->assertSame($sender, $message->getSender());

        $this->assertSame($length, $message->getLength());
    }
}
