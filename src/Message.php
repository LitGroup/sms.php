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

class Message
{
    /**
     * @var string
     */
    private $sender;

    /**
     * @var string[]
     */
    private $receivers;

    /**
     * @var string
     */
    private $body;


    public function __construct($body, array $receivers, $sender = null)
    {
        $this->body = $body;
        $this->$receivers = array_values($receivers);
        $this->sender = $sender;
    }

    public function getSender()
    {
        return $this->sender;
    }

    public function getReceivers()
    {
        return $this->receivers;
    }

    public function getBody()
    {
        return $this->body;
    }
}