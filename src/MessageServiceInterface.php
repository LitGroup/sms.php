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

use LitGroup\Sms\Exception\GatewayException;
use LitGroup\Sms\Logger\MessageLoggerInterface;

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
     * @throws GatewayException          If gateway problem occurred.
     * @throws \InvalidArgumentException If required message fields are not fully filled.
     */
    public function sendMessage(Message $message);

    /**
     * Sets message logger.
     *
     * All successfully sent messages will be logged in a given logger.
     *
     * @param MessageLoggerInterface $messageLogger
     *
     * @return void
     */
    public function setMessageLogger(MessageLoggerInterface $messageLogger);
}