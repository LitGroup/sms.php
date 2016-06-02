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
 * Exception thrown if gateway is a SMS gateway is unavailable or transport problem occurred.
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
class GatewayException extends \Exception implements ExceptionInterface
{
    /**
     * GatewayException constructor.
     * 
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}