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
use LitGroup\Sms\Exception\SmsException;
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
            ->with($this->identicalTo($message))
        ;

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
            ->with($this->identicalTo($message))
        ;

        $this->messageService->sendMessage($message);
    }

    /**
     * @test
     */
    public function shouldThrowSmsExceptionIfGatewayProblemOccurred()
    {
        $gatewayException = $this->getMockForGatewayException();
        $this->gateway
            ->expects($this->once())
            ->method('sendMessage')
            ->willThrowException($gatewayException)
        ;

        try {
            $this->messageService->sendMessage($this->getMockForMessage());
        } catch (SmsException $e) {
            $this->assertSame($gatewayException, $e->getPrevious(), 'GatewayException must be attached');
            // Check log:
            $logEntry = $this->logger->getAlerts()[0];
            $this->assertSame('Problem with SMS Gateway has occurred.', $logEntry['message']);
            $this->assertSame($gatewayException, $logEntry['context']['exception']);

            return;
        }

        $this->fail('SmsException should be thrown');

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
