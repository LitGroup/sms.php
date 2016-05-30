<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests\LitGroup\Sms;

use LitGroup\Sms\Message;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    const BODY = 'Hola!';
    const BODY_LENGTH = 5;

    const RECIPIENT_A = '+71231111111';
    const RECIPIENT_B = '+71232222222';
    const RECIPIENT_C = '+71233333333';

    const SENDER = 'Company ltd.';

    public function testConstructMinimal()
    {
        $msg = new Message(
            self::BODY,
            [
                self::RECIPIENT_A,
                self::RECIPIENT_B,
                self::RECIPIENT_C,
            ]
        );
        $this->assertSame(self::BODY, $msg->getBody());
        $this->assertSame(
            [
                self::RECIPIENT_A,
                self::RECIPIENT_B,
                self::RECIPIENT_C,
            ],
            $msg->getRecipients()
        );
        $this->assertNull($msg->getSender());
    }

    public function testConstructWithSender()
    {
        $msg = new Message(self::BODY, [self::RECIPIENT_A], self::SENDER);
        $this->assertSame(self::BODY, $msg->getBody());
        $this->assertSame([self::RECIPIENT_A], $msg->getRecipients());
        $this->assertSame(self::SENDER, $msg->getSender());
    }

    public function testGetLength()
    {
        $msg = new Message(self::BODY, [self::RECIPIENT_A]);
        $this->assertSame(self::BODY_LENGTH, $msg->getLength());
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function testBodyCannotBeNull()
    {
        new Message(null, [self::RECIPIENT_A]);
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function testBodyMustBeAString()
    {
        new Message(new \stdClass(), [self::RECIPIENT_A]);
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function testBodyCannotBeEmptyString()
    {
        new Message('', [self::RECIPIENT_A]);
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function testListOfRecipientsCannotBeEmpty()
    {
        new Message(self::BODY, []);
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function testRecipientCannotBeNull()
    {
        new Message(self::BODY, [null]);
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function testRecipientNumberMustBeAString()
    {
        new Message(self::BODY, [new \stdClass]);
    }

    public function getInvalidRecipientFormatTests()
    {
        return [
            [''],
            ['1234567890'],
            ['++123456789'],
            ['+7 (495) 123-45-67'],
            ['alphabetical'],
            ['+alphabetical'],
        ];
    }

    /**
     * @dataProvider getInvalidRecipientFormatTests
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function testInvalidRecipientFormat($recipient)
    {
        new Message(self::BODY, [$recipient]);
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function testSenderMustBeAString()
    {
        new Message(self::BODY, [self::RECIPIENT_A], new \stdClass());
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function testSenderCannotBenEmptyString()
    {
        new Message(self::BODY, [self::RECIPIENT_A], '');
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function testSenderCannotContainWhitespaceOnlyCharacters()
    {
        new Message(self::BODY, [self::RECIPIENT_A], '      ');
    }
}