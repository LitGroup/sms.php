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

    /**
     * @test
     */
    public function shouldBeConstructedWithBodyAndRecipientsList()
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

    /**
     * @test
     */
    public function canBeConstructedWithNameOfSender()
    {
        $msg = new Message(self::BODY, [self::RECIPIENT_A], self::SENDER);
        $this->assertSame(self::BODY, $msg->getBody());
        $this->assertSame([self::RECIPIENT_A], $msg->getRecipients());
        $this->assertSame(self::SENDER, $msg->getSender());
    }

    /**
     * @test
     */
    public function shouldCalculateLengthOfBodyInCharacters()
    {
        $msg = new Message(self::BODY, [self::RECIPIENT_A]);
        $this->assertSame(self::BODY_LENGTH, $msg->getLength());
    }

    /**
     * @test
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function shouldThrowAnExceptionIfBodyIsNull()
    {
        new Message(null, [self::RECIPIENT_A]);
    }

    /**
     * @test
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function shouldThrowAnExceptionIfBodyIsNotAString()
    {
        new Message(new \stdClass(), [self::RECIPIENT_A]);
    }

    /**
     * @test
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function shouldThrowAnExceptionIfBodyIsAnEmptyString()
    {
        new Message('', [self::RECIPIENT_A]);
    }

    /**
     * @test
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function shouldThrowAnExceptionIfListOfRecipientsIsEmpty()
    {
        new Message(self::BODY, []);
    }

    /**
     * @test
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function shouldThrowAnExceptionIfAnyOfRecipientsIsNull()
    {
        new Message(self::BODY, [null]);
    }

    /**
     * @test
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function shouldThrowAnExceptionIfAnyOfRecipientsRepresentedByNotAString()
    {
        new Message(self::BODY, [new \stdClass]);
    }

    public function getInvalidRecipients()
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
     * @test
     * @dataProvider getInvalidRecipients
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function shouldThrowAnExceptionIfFormatOfRecipientStringIsInvalid($recipient)
    {
        new Message(self::BODY, [$recipient]);
    }

    /**
     * @test
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function shouldThrowAnExceptionIfSenderIsNotAString()
    {
        new Message(self::BODY, [self::RECIPIENT_A], new \stdClass());
    }

    /**
     * @test
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function shouldThrowAnExceptionIfNameOfSenderIsAnEmptyString()
    {
        new Message(self::BODY, [self::RECIPIENT_A], '');
    }

    /**
     * @test
     * @expectedException \LitGroup\Sms\Exception\InvalidArgumentException
     */
    public function shouldThrowAnExceptionIfNameOfSenderContainsWhitespaceCharacterOnly()
    {
        new Message(self::BODY, [self::RECIPIENT_A], '      ');
    }
}