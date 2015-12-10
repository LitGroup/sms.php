<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) LitGroup <http://litgroup.ru/>
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
 */
interface GatewayInterface
{
    /**
     * Sends message.
     *
     * @param Message $message It is expected that the facility has verified and contains correct information.
     *
     * @return void
     *
     * @throws GatewayException
     */
    public function sendMessage(Message $message);
}