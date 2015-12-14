<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) LitGroup <http://litgroup.ru/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LitGroup\Sms\Exception;

use Exception;

/**
 * Class GatewayTransportException
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
class GatewayTransferException extends GatewayException
{
    /**
     * GatewayTransferException constructor.
     *
     * @param string $message
     * @param Exception $previous
     */
    public function __construct($message, Exception $previous)
    {
        parent::__construct($message, 0, $previous);
    }

}