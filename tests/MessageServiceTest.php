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

use LitGroup\Sms\Exception\GatewayException;
use LitGroup\Sms\Gateway\GatewayInterface;
use LitGroup\Sms\Logger\MessageLoggerInterface;
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
     * @var MessageLoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageLogger;

    /**
     * @var TestLogger
     */
    private $logger;


    protected function setUp()
    {
        $this->gateway = $this->getMock(GatewayInterface::class);
        $this->messageLogger = $this->getMock(MessageLoggerInterface::class);
        $this->logger = new TestLogger();
        $this->messageService = new MessageService($this->gateway);
        $this->messageService->setMessageLogger($this->messageLogger);
        $this->messageService->setLogger($this->logger);
    }

    protected function tearDown()
    {
        $this->messageService = null;
        $this->gateway = null;
        $this->messageLogger = null;
        $this->logger = null;
    }

    public function testSendMessage()
    {
        $message = $this->getMessage();

        $this->gateway
            ->expects($this->once())
            ->method('sendMessage')
            ->with($this->identicalTo($message));
        $this->messageLogger
            ->expects($this->once())
            ->method('addMessage')
            ->with($this->equalTo($message));

        $this->messageService->sendMessage($message);

        $this->assertCount(1, $this->logger->getInfos());
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

        $this->messageLogger
            ->expects($this->never())
            ->method('addMessage');

        $this->messageService->sendMessage($this->getMessage());

        $this->assertCount(1, $this->logger->getAlerts());
    }

    /**
     * @return Message
     */
    private function getMessage()
    {
        return new Message(
            self::MESSAGE_BODY,
            [
                self::RECIPIENT_1,
                self::RECIPIENT_2,
            ],
            self::SENDER
        );
    }
}
