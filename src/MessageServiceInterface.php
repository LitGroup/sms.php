<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) LitGroup <http://litgroup.ru/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LitGroup\Sms;

use LitGroup\Sms\Exception\GatewayException;

/**
 * Interface MessageServiceInterface.
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
interface MessageServiceInterface
{
    /**
     * Sends single message.
     *
     * @param Message $message
     *
     * @return void
     *
     * @throws GatewayException          If gateway cannot send the message.
     * @throws \InvalidArgumentException If message body is empty or list of recipients is empty or invalid.
     */
    public function sendMessage(Message $message);
}