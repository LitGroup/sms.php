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
 * Message.
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
class Message
{
    /**
     * @var string
     */
    private $sender;

    /**
     * @var string[]
     */
    private $recipients;

    /**
     * @var string
     */
    private $body;


    /**
     * Message constructor.
     *
     * @param string $body Text of message (UTF-8 string).
     * @param array $recipients List of phone numbers of recipients. Phone number should be in the format
     *                          "+7000123456789" or "7000123456789".
     * @param null $sender      Identifier of sender. (For example "MyCompany"). Identifier should be allowed by
     *                          SMS-Gateway provider.
     */
    public function __construct($body, array $recipients, $sender = null)
    {
        $this->body = $body;
        $this->recipients = array_values($recipients);
        $this->sender = $sender;
    }

    /**
     * Returns the name of sender.
     *
     * @return string|null
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Returns list of phone number of recipients.
     *
     * @return string[]
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * Returns the body of message.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Returns the length of the body in characters.
     *
     * @return integer
     */
    public function getLength()
    {
        return mb_strlen($this->getBody(), 'UTF-8');
    }
}