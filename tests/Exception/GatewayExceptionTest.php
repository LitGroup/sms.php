<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests\LitGroup\Sms\Exception\Exception;

use LitGroup\Sms\Exception\GatewayException;

class GatewayExceptionTest extends \PHPUnit_Framework_TestCase
{
    const MESSAGE = 'Some message';

    /**
     * @test
     */
    public function messageMustBeGiven()
    {
        $e = new GatewayException(self::MESSAGE);
        $this->assertSame(self::MESSAGE, $e->getMessage());
    }

    /**
     * @test
     */
    public function previousExceptionMayBeAttached()
    {
        $prev = $this->getMock(\Exception::class, [], [], '', false, false);

        $e = new GatewayException(self::MESSAGE, $prev);
        $this->assertSame(self::MESSAGE, $e->getMessage());
        $this->assertSame($prev, $e->getPrevious());
    }
}
