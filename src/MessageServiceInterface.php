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
     * Returns new instance of Message.
     *
     * @return Message
     */
    public function createMessage();

    /**
     * Sends single message.
     *
     * @param Message $message
     *
     * @return void
     *
     * @throws GatewayException          If gateway problem occurred.
     * @throws \InvalidArgumentException If required message fields are not fully filled.
     */
    public function sendMessage(Message $message);
}