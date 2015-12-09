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
     */
    public function sendMessage(Message $message);
}