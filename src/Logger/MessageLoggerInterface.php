<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) LitGroup <http://litgroup.ru/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LitGroup\Sms\Logger;

use LitGroup\Sms\Message;

/**
 * MessageLoggerInterface.
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
interface MessageLoggerInterface
{
    /**
     * Adds message into the log.
     *
     * @param Message $message
     *
     * @return void
     */
    public function addMessage(Message $message);
}