<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) LitGroup <http://litgroup.ru/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests\LitGroup\Sms;

use LitGroup\Sms\Message;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function getTestsData()
    {
        return [
            /* body, recipients, sender, length */
            ['Hello!',        ['+79991234567', '76669999999'], ['+79991234567', '+76669999999'], null],
            ['Hello!',        ['+79991234567', '76669999999'], ['+79991234567', '+76669999999'], 'LitGroup'],
            ['Привет, друг!', ['+79991234567', '76669999999'], ['+79991234567', '+76669999999'], null],
        ];
    }

    /**
     * @dataProvider getTestsData
     */
    public function testConstructor($body, array $recipients, array $recipientsCanonical, $sender)
    {
        $message = new Message($body, $recipients, $sender);

        $this->assertSame($body, $message->getBody());
        $this->assertSame($recipientsCanonical, $message->getRecipients());
        $this->assertSame($sender, $message->getSender());
    }

    public function testBody()
    {
        $message = new Message();
        $this->assertNull($message->getBody());

        $this->assertSame($message, $message->setBody('How are you?'));
        $this->assertSame('How are you?', $message->getBody());
    }

    public function testSender()
    {
        $message = new Message();
        $this->assertNull($message->getSender());

        $this->assertSame($message, $message->setSender('LitGroup'));
        $this->assertSame('LitGroup', $message->getSender());
    }

    public function getLengthTests()
    {
        return [
            [0, null],
            [0, ''],
            [12, 'How are you?'],
            [7, 'Как ты?'],
        ];
    }

    /**
     * @dataProvider getLengthTests
     */
    public function testLength($length, $body)
    {
        $message = new Message();
        $this->assertSame(0, $message->getLength());

        $message->setBody($body);
        $this->assertSame($length, $message->getLength());
    }

    public function getRecipientsTests()
    {
        return [
            [
                [
                    '+71111234567',
                    '72221234567',
                    '73331234567',
                ],
                [
                    '+71111234567',
                    '+72221234567',
                    '+73331234567',
                ]
            ]
        ];
    }

    public function getInvalidRecipientsTests()
    {
        return [
            [null],
            [''],
            ['   '],
            ['not a number'],
            [123456789],
        ];
    }

    /**
     * @dataProvider getRecipientsTests
     */
    public function testAddRecipient($recipients, $expected)
    {
        $message = new Message();
        $this->assertSame([], $message->getRecipients());

        foreach ($recipients as $recipient) {
            $this->assertSame($message, $message->addRecipient($recipient));
        }
        $this->assertSame($expected, $message->getRecipients());
    }

    /**
     * @dataProvider getInvalidRecipientsTests
     * @expectedException \InvalidArgumentException
     */
    public function testAddRecipient_InvalidValue($recipient)
    {
        (new Message())->addRecipient($recipient);
    }

    /**
     * @dataProvider getRecipientsTests
     */
    public function testSetRecipients($recipients, $expected)
    {
        $message = new Message();
        $message->setRecipients(['+71234567890']);

        $this->assertSame($message, $message->setRecipients($recipients));
        $this->assertSame($expected, $message->getRecipients());

        $message->setRecipients([]);
        $this->assertSame([], $message->getRecipients());
    }

    /**
     * @dataProvider getInvalidRecipientsTests
     * @expectedException \InvalidArgumentException
     */
    public function testSetRecipients_InvalidValue($recipient)
    {
        (new Message())->setRecipients([$recipient]);
    }

}