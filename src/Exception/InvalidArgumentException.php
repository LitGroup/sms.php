<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LitGroup\Sms\Exception;

/**
 * Exception thrown if an argument does not match with the expected value.
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
class InvalidArgumentException extends \InvalidArgumentException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}