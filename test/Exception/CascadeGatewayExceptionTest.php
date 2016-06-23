<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Test\LitGroup\Sms\Exception;

use LitGroup\Sms\Exception\CascadeGatewayException;
use LitGroup\Sms\Exception\GatewayException;

class CascadeGatewayExceptionTest extends \PHPUnit_Framework_TestCase
{
    const STR_A = 'String A';
    const STR_B = 'String B';

    /**
     * @test
     */
    public function shouldContainInformationAboutAllExceptionsInACascade()
    {
        $exception = new CascadeGatewayException([
            $this->getMockForGatewayException(self::STR_A),
            $this->getMockForGatewayException(self::STR_B)
        ]);

        $this->assertSame('All SMS gateways are inoperative (2 gateways total).', $exception->getMessage());
        $this->assertCount(2, $exception->getCascadeExceptions());
        $this->assertSame(1, substr_count((string) $exception, self::STR_A));
        $this->assertSame(1, substr_count((string) $exception, self::STR_B));
    }

    /**
     * @param string $string
     *
     * @return GatewayException|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockForGatewayException($string)
    {
        $mock = $this->getMock(GatewayException::class, [], [], '', false, false);
        $mock
            ->expects($this->any())
            ->method('__toString')
            ->willReturn($string);

        return $mock;
    }
}