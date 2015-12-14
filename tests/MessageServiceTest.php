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
use Tests\LitGroup\Sms\Log\TestLogger;

class MessageServiceTest extends \PHPUnit_Framework_TestCase
{
    const MESSAGE_BODY = 'How are you?';
    const RECIPIENT_1 = '+71112223344';
    const RECIPIENT_2 = '+72223334455';
    const SENDER = 'LitGroup';

    /**
     * @var MessageService
     */
    private $messageService;

    /**
     * @var GatewayInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $gateway;

    /**
     * @var TestLogger
     */
    private $logger;


    protected function setUp()
    {
        $this->gateway = $this->getMock(GatewayInterface::class);
        $this->logger = new TestLogger();
        $this->messageService = new MessageService($this->gateway);
        $this->messageService->setLogger($this->logger);
    }

    protected function tearDown()
    {
        $this->messageService = null;
        $this->gateway = null;
        $this->logger = null;
    }

    public function testCreateMessage()
    {
        $this->assertInstanceOf(Message::class, $this->messageService->createMessage());
    }

    public function testSendMessage()
    {
        $message = $this->getMessage();

        $this->gateway
            ->expects($this->once())
            ->method('sendMessage')
            ->with($this->identicalTo($message));

        $this->messageService->sendMessage($message);

        $this->assertCount(1, $this->logger->getInfos());
    }

    public function getSendMessageWithInvalidMessageTests()
    {
        return [
            [new Message()],
            [new Message('', [self::RECIPIENT_1])],
            [new Message('  ', [self::RECIPIENT_1])],
            [new Message(self::MESSAGE_BODY)],
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

    /**
     * @expectedException \LitGroup\Sms\Exception\GatewayException
     */
    public function testSendMessage_GatewayThrowsException()
    {
        $this->gateway
            ->expects($this->once())
            ->method('sendMessage')
            ->willThrowException($this->getMockForAbstractClass(GatewayException::class, [], '', false, false));

        $this->messageService->sendMessage($this->getMessage());

        $this->assertCount(1, $this->logger->getAlerts());
    }

    /**
     * @return Message
     */
    private function getMessage()
    {
        return (new Message())
            ->setBody(self::MESSAGE_BODY)
            ->addRecipient(self::RECIPIENT_1)
            ->addRecipient(self::RECIPIENT_2)
            ->setSender(self::SENDER);
    }
}
