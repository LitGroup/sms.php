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
 * Class GatewayErrorResponseException
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
class GatewayErrorResponseException extends GatewayException
{
    /**
     * GatewayErrorResponseException constructor.
     *
     * @param string $message
     * @param integer $code
     */
    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }

}