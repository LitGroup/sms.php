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

use LitGroup\Sms\Message;

/**
 * NullGateway usable for development.
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 *
 * @codeCoverageIgnore
 */
class NullGateway implements GatewayInterface
{
    /**
     * @inheritDoc
     */
    public function sendMessage(Message $message)
    {
        // Nothing to do.
    }
}