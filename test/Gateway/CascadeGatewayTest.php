<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Test\LitGroup\Sms\Gateway;

use LitGroup\Sms\Exception\CascadeGatewayException;
use LitGroup\Sms\Exception\GatewayException;
use LitGroup\Sms\Gateway\CascadeGateway;
use LitGroup\Sms\Gateway\GatewayInterface;
use LitGroup\Sms\Message;
use Test\LitGroup\Sms\Fixtures\TestLogger;

class CascadeGatewayTest extends \PHPUnit_Framework_TestCase
{
    const GATEWAY_A = 'gateway-a';
    const GATEWAY_B = 'gateway-b';

    /**
     * @var CascadeGateway
     */
    private $cascadeGateway;

    /**
     * @var TestLogger
     */
    private $logger;

    /**
     * @var GatewayInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $gatewayA;

    /**
     * @var GatewayInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $gatewayB;

    protected function setUp()
    {
        $this->logger = new TestLogger();
        $this->gatewayA = $this->getMockForGateway();
        $this->gatewayB = $this->getMockForGateway();
        $this->cascadeGateway = new CascadeGateway(
            [
                self::GATEWAY_A => $this->gatewayA,
                self::GATEWAY_B => $this->gatewayB
            ],
            $this->logger
        );
    }

    protected function tearDown()
    {
        $this->logger = null;
        $this->cascadeGateway = null;
        $this->gatewayA = null;
        $this->gatewayB = null;
    }

    /**
     * @test
     */
    public function shouldSendAMessageViaTheFirstOperableGateway()
    {
        $message = $this->getMockForMessage();

        $this->gatewayA
            ->expects($this->once())
            ->method('sendMessage')
            ->with($this->identicalTo($message));
        $this->gatewayB
            ->expects($this->never())
            ->method('sendMessage');

        $this->cascadeGateway->sendMessage($message);
    }

    /**
     * @test
     */
    public function shouldCascadeCallNextGatewayIfPreviousFailed()
    {
        $message = $this->getMockForMessage();
        $exceptionA = $this->getGatewayException();

        $this->gatewayA
            ->expects($this->once())
            ->method('sendMessage')
            ->with($this->identicalTo($message))
            ->willThrowException($exceptionA);
        $this->gatewayB
            ->expects($this->once())
            ->method('sendMessage')
            ->with($this->identicalTo($message));

        $this->cascadeGateway->sendMessage($message);

        $this->assertCount(1, $this->logger->getWarnings(), 'Warning should be logged if some gateway inoperable');
    }

    /**
     * @test
     */
    public function shouldThrowCascadeGatewayExceptionIfAllGatewaysAreInoperable()
    {
        $message = $this->getMockForMessage();

        $exceptionA = $this->getGatewayException();
        $exceptionB = $this->getGatewayException();

        $this->gatewayA
            ->expects($this->once())
            ->method('sendMessage')
            ->with($this->identicalTo($message))
            ->willThrowException($exceptionA);
        $this->gatewayB
            ->expects($this->once())
            ->method('sendMessage')
            ->with($this->identicalTo($message))
            ->willThrowException($exceptionB);

        try {
            $this->cascadeGateway->sendMessage($message);
        } catch (CascadeGatewayException $e) {
            $this->assertCount(2, $this->logger->getWarnings());

            $this->assertCount(2, $e->getCascadeExceptions());
            $this->assertSame(
                [
                    self::GATEWAY_A => $exceptionA,
                    self::GATEWAY_B => $exceptionB,
                ],
                $e->getCascadeExceptions()
            );

            return;
        }

        $this->fail('CascadeGatewayException was not thrown');
    }

    /**
     * @return GatewayInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockForGateway()
    {
        return $this->getMock(GatewayInterface::class);
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
    private function getGatewayException()
    {
        return new GatewayException('Big bang');
    }
}