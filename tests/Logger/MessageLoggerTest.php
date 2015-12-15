<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) LitGroup <http://litgroup.ru/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests\LitGroup\Sms\Logger;

use LitGroup\Sms\Logger\MessageLogger;
use LitGroup\Sms\Message;

class MessageLoggerTest extends \PHPUnit_Framework_TestCase
{
    const RECIPIENT_1 = '+71111111111';
    const RECIPIENT_2 = '+72222222222';
    const MESSAGE_BODY = 'How are you?';


    public function testEmptyLogger()
    {
        $logger = new MessageLogger();

        $this->assertSame(0, $logger->count());
        $this->assertSame([], $logger->getMessages());

        return $logger;
    }

    /**
     * @depends testEmptyLogger
     */
    public function testAddMessage(MessageLogger $logger)
    {
        $messageA = $this->getMessage(self::RECIPIENT_1);
        $messageB = $this->getMessage(self::RECIPIENT_2);

        $logger->addMessage($messageA);
        $logger->addMessage($messageB);

        $this->assertSame(2, $logger->count());

        // Message should be cloned in the moment of logging.
        $this->assertEquals($messageA, $logger->getMessages()[0]);
        $this->assertNotSame($messageA, $logger->getMessages()[0]);

        $this->assertEquals($messageB, $logger->getMessages()[1]);
        $this->assertNotSame($messageB, $logger->getMessages()[1]);

        return $logger;
    }

    /**
     * @depends testAddMessage
     */
    public function testClean(MessageLogger $logger)
    {
        $logger->clean();

        $this->assertSame(0, $logger->count());
        $this->assertSame([], $logger->getMessages());
    }

    /**
     * @param string $recipient
     *
     * @return Message
     */
    private function getMessage($recipient)
    {
        return new Message(self::MESSAGE_BODY, [$recipient]);
    }
}
