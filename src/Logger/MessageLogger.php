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
 * MessageLogger.
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
class MessageLogger implements MessageLoggerInterface, \Countable
{
    /**
     * @var Message[]
     */
    private $messages = [];

    /**
     * @inheritDoc
     */
    public function addMessage(Message $message)
    {
        array_push($this->messages, clone $message);
    }

    /**
     * Returns all logged messages.
     *
     * @return Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Returns number of logged messages.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->messages);
    }

    /**
     * Removes all logged messages.
     */
    public function clean()
    {
        $this->messages = [];
    }
}