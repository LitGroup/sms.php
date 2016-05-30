<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LitGroup\Sms\Logger;

use LitGroup\Sms\Message;

/**
 * NullMessageLogger.
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 *
 * @codeCoverageIgnore
 */
class NullMessageLogger implements MessageLoggerInterface
{
    /**
     * @inheritDoc
     */
    public function addMessage(Message $message)
    {
        // Nothing to do. It's a null-object.
    }
}