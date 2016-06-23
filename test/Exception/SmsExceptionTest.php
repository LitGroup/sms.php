<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Test\LitGroup\Sms\Exception\Exception;

use LitGroup\Sms\Exception\SmsException;

class SmsExceptionTest extends \PHPUnit_Framework_TestCase
{
    const MESSAGE = 'Some exception occurred';

    /**
     * @test
     */
    public function shouldContainAMessage()
    {
        $e = new SmsException(self::MESSAGE);

        $this->assertSame(self::MESSAGE, $e->getMessage());
        $this->assertNull($e->getPrevious());
    }

    /**
     * @test
     */
    public function canContainPreviousMessageBesidesAMessage()
    {
        $prev = $this->getMockForException();
        $e = new SmsException(self::MESSAGE, $prev);

        $this->assertSame(self::MESSAGE, $e->getMessage());
        $this->assertSame($prev, $prev);
    }

    /**
     * @return \Exception|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockForException()
    {
        return $this->getMock(\Exception::class, [], [], '', false, false);
    }
}
