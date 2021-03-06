<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LitGroup\Sms\Gateway;

use LitGroup\Sms\Exception\GatewayException;
use LitGroup\Sms\Message;

/**
 * Interface should be implemented for a some vendor of SMS-gateway.
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 *
 * @api
 */
interface GatewayInterface
{
    /**
     * Sends message.
     *
     * @param Message $message
     *
     * @return void
     *
     * @throws GatewayException
     */
    public function sendMessage(Message $message);
}