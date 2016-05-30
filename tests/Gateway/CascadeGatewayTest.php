<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests\LitGroup\Sms\Gateway;

use LitGroup\Sms\Exception\CascadeGatewayException;
use LitGroup\Sms\Exception\GatewayException;
use LitGroup\Sms\Exception\GatewayUnavailableException;
use LitGroup\Sms\Gateway\CascadeGateway;
use LitGroup\Sms\Gateway\GatewayInterface;
use LitGroup\Sms\Message;
use Tests\LitGroup\Sms\Log\TestLogger;

class CascadeGatewayTest extends \PHPUnit_Framework_TestCase
{
    const GATEWAY_A = 'gateway-a';
    const GATEWAY_B = 'gateway-b';
    const GATEWAY_C = 'gateway-c';

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

    /**
     * @var GatewayInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $gatewayC;


    protected function setUp()
    {
        $this->logger = new TestLogger();
        $this->cascadeGateway = new CascadeGateway();
        $this->cascadeGateway->setLogger($this->logger);
        $this->cascadeGateway
            ->addGateway(self::GATEWAY_A, $this->gatewayA = $this->getMockForGateway())
            ->addGateway(self::GATEWAY_B, $this->gatewayB = $this->getMockForGateway())
            ->addGateway(self::GATEWAY_C, $this->gatewayC = $this->getMockForGateway());
    }

    protected function tearDown()
    {
        $this->logger = null;
        $this->cascadeGateway = null;
        $this->gatewayA = null;
        $this->gatewayB = null;
        $this->gatewayC = null;
    }

    public function testSendMessageA()
    {
        $message = $this->getMessage();

        $this->gatewayA
            ->expects($this->once())
            ->method('sendMessage')
            ->with($this->identicalTo($message));
        $this->gatewayB
            ->expects($this->never())
            ->method('sendMessage');
        $this->gatewayC
            ->expects($this->never())
            ->method('sendMessage');

        $this->cascadeGateway->sendMessage($message);
    }

    public function testSendMessageB()
    {
        $message = $this->getMessage();

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
        $this->gatewayC
            ->expects($this->once())
            ->method('sendMessage');

        $this->cascadeGateway->sendMessage($message);

        $this->assertCount(2, $this->logger->getWarnings());
    }

    public function testSendMessage_AllGatewaysFailed()
    {
        $message = $this->getMessage();

        $exceptionA = $this->getGatewayException();
        $exceptionB = $this->getGatewayException();
        $exceptionC = $this->getGatewayException();

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
        $this->gatewayC
            ->expects($this->once())
            ->method('sendMessage')
            ->with($this->identicalTo($message))
            ->willThrowException($exceptionC);

        try {
            $this->cascadeGateway->sendMessage($message);
        } catch (CascadeGatewayException $e) {
            $this->assertCount(3, $e->getCascadeExceptions());
            $this->assertArrayHasKey(self::GATEWAY_A, $e->getCascadeExceptions());
            $this->assertSame($exceptionA, $e->getCascadeExceptions()[self::GATEWAY_A]);
            $this->assertArrayHasKey(self::GATEWAY_B, $e->getCascadeExceptions());
            $this->assertSame($exceptionB, $e->getCascadeExceptions()[self::GATEWAY_B]);
            $this->assertArrayHasKey(self::GATEWAY_C, $e->getCascadeExceptions());
            $this->assertSame($exceptionC, $e->getCascadeExceptions()[self::GATEWAY_C]);

            $this->assertCount(3, $this->logger->getWarnings());

            return;
        }

        $this->fail('CascadeGatewayException should be thrown.');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddGatewayForDuplicateName()
    {
        $this->cascadeGateway->addGateway(self::GATEWAY_A, $this->getMockForGateway());
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
    private function getMessage()
    {
        return $this->getMock(Message::class, [], [], '', false, false);
    }

    /**
     * @return GatewayException
     */
    private function getGatewayException()
    {
        return new GatewayUnavailableException('Big bang');
    }
}