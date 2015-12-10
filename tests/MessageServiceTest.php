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

use LitGroup\Sms\Exception\GatewayException;
use LitGroup\Sms\Gateway\GatewayInterface;
use LitGroup\Sms\Message;
use LitGroup\Sms\MessageService;

class MessageServiceTest extends \PHPUnit_Framework_TestCase
{
    const RECIPIENT_1 = '+79991234567';
    const RECIPIENT_1_CANONICAL = '79991234567';

    const RECIPIENT_2 = '76661234567';
    const RECIPIENT_2_CANONICAL = '76661234567';

    const MESSAGE_BODY = 'Welcome';
    const SENDER = 'LitGroup';

    /**
     * @var MessageService
     */
    private $messageService;

    /**
     * @var GatewayInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $gateway;


    protected function setUp()
    {
        $this->gateway = $this->getMock(GatewayInterface::class);
        $this->messageService = new MessageService($this->gateway);
    }

    protected function tearDown()
    {
        $this->gateway = null;
        $this->messageService = null;
    }

    public function getSendMessageWithInvalidMessageTests()
    {
        return [
            [new Message(null, [self::RECIPIENT_1])],
            [new Message('', [self::RECIPIENT_1])],
            [new Message('  ', [self::RECIPIENT_1])],

            [new Message(self::MESSAGE_BODY, [])],
            [new Message(self::MESSAGE_BODY, ['not a phone number'])],
        ];
    }

    /**
     * @dataProvider getSendMessageWithInvalidMessageTests
     *
     * @expectedException \InvalidArgumentException
     */
    public function testSendMessageWithInvalidMessage(Message $message)
    {
        $this->gateway
            ->expects($this->never())
            ->method('sendMessage');

        $this->messageService->sendMessage($message);
    }

    public function testSendMessage()
    {
        $message = new Message(
            self::MESSAGE_BODY,
            [
                self::RECIPIENT_1,
                self::RECIPIENT_1,
                self::RECIPIENT_1,
                self::RECIPIENT_2,
                self::RECIPIENT_2,
            ],
            self::SENDER
        );

        $this->gateway
            ->expects($this->once())
            ->method('sendMessage')
            ->with(
                $this->equalTo(
                    new Message(
                        self::MESSAGE_BODY,
                        [
                            self::RECIPIENT_1_CANONICAL,
                            self::RECIPIENT_2_CANONICAL],
                        self::SENDER
                    )
                )
            );

        $this->messageService->sendMessage($message);
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\GatewayException
     */
    public function testSendMessageWhenGatewayThrowsAnException()
    {
        $message = new Message(self::MESSAGE_BODY, [self::RECIPIENT_1]);

        $this->gateway
            ->expects($this->once())
            ->method('sendMessage')
            ->with($this->anything())
            ->willThrowException(new GatewayException());

        $this->messageService->sendMessage($message);
    }

    /**
     * @return GatewayException|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockForGatewayException()
    {
        return $this->getMock(GatewayException::class, [], [], '', false, false);
    }
}
