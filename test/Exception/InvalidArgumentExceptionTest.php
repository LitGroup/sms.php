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

use LitGroup\Sms\Exception\InvalidArgumentException;

class InvalidArgumentExceptionTest extends \PHPUnit_Framework_TestCase
{
    const MESSAGE = 'Some message';

    public function testConstruct()
    {
        $e = new InvalidArgumentException(self::MESSAGE);
        $this->assertInstanceOf(\InvalidArgumentException::class, $e);
        $this->assertSame(self::MESSAGE, $e->getMessage());
        $this->assertSame(0, $e->getCode());
    }
}
