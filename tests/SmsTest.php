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
use LitGroup\Sms\Message;
use LitGroup\Sms\Sms;
use Tests\LitGroup\Sms\Fixtures\TestLogger;

class SmsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Sms
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
        $this->messageService = new Sms($this->gateway, $this->logger);
    }

    protected function tearDown()
    {
        $this->messageService = null;
        $this->gateway = null;
        $this->logger = null;
    }

    public function canBeConstructedWithoutLogger()
    {
        $sms = new Sms($this->gateway);
        $message = $this->getMockForMessage();
        $this->gateway
            ->expects($this->once())
            ->method('sendMessage')
            ->with($this->identicalTo($message));

        $sms->sendMessage($message);
    }

    /**
     * @test
     */
    public function shouldSendAMessageViaTheGateway()
    {
        $message = $this->getMockForMessage();

        $this->gateway
            ->expects($this->once())
            ->method('sendMessage')
            ->with($this->identicalTo($message));

        $this->messageService->sendMessage($message);
    }

    /**
     * @test
     * @expectedException \LitGroup\Sms\Exception\GatewayException
     */
    public function shouldLogAndRethrowGatewayException()
    {
        $this->gateway
            ->expects($this->once())
            ->method('sendMessage')
            ->willThrowException($this->getMockForGatewayException());

        $this->messageService->sendMessage($this->getMockForMessage());
        $this->assertCount(1, $this->logger->getAlerts(), 'Should log alert if gateway throws an exception');
    }

    /**
     * @return Message
     */
    private function getMockForMessage()
    {
        return $this->getMock(Message::class, [], [], '', false, false);
    }

    /**
     * @return GatewayException
     */
    private function getMockForGatewayException()
    {
        return $this->getMockForAbstractClass(GatewayException::class, [], '', false, false);
    }
}