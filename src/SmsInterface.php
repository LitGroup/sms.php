<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LitGroup\Sms;

use LitGroup\Sms\Exception\SmsException;

/**
 * Interface MessageServiceInterface.
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 *
 * @api
 */
interface SmsInterface
{
    /**
     * Sends single message.
     *
     * @param Message $message
     *
     * @return void
     *
     * @throws SmsException If message cannot be sent.
     */
    public function sendMessage(Message $message);
}