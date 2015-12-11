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
    public function __construct($body = null, array $recipients = [], $sender = null)
    {
        $this
            ->setBody($body)
            ->setRecipients($recipients)
            ->setSender($sender);
    }

    /**
     * Sets the message body.
     *
     * @param string $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Returns the message body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Returns the length of the message body in characters.
     *
     * @return integer
     */
    public function getLength()
    {
        return mb_strlen($this->getBody(), 'UTF-8');
    }

    /**
     * Adds recipient.
     *
     * @param string $recipient A phone number in the one of formats: "+71234567890" or "71234567890".
     *
     * @return $this
     *
     * @throws \InvalidArgumentException If $recipient is not a string or format is invalid.
     */
    public function addRecipient($recipient)
    {
        array_push(
            $this->recipients,
            $this->canonicalizeRecipient($recipient)
        );

        return $this;
    }

    /**
     * Sets/replaces list of recipients.
     *
     * @param string[] $recipients A list of phone numbers in the one of formats: "+71234567890" or "71234567890".
     *
     * @return $this
     *
     * @throws \InvalidArgumentException If one of recipients is not a string or format is invalid.
     */
    public function setRecipients(array $recipients)
    {
        $this->recipients = [];

        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }

        return $this;
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
     * Sets the name of sender.
     *
     * @param string $sender
     *
     * @return $this
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
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
     * @param string $recipient
     *
     * @return string
     *
     * @throws \InvalidArgumentException If $recipient is not a string or format is invalid.
     */
    private function canonicalizeRecipient($recipient)
    {
        if (!is_string($recipient)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Recipient should be a string, but "%s" given.',
                    is_object($recipient) ? get_class($recipient) : gettype($recipient)
                )
            );
        }

        if (preg_match('/^\+?(\d+)$/Ds', $recipient, $matches) !== 1) {
            throw new \InvalidArgumentException(
                sprintf('Invalid format of recipient. Should be a string like "+71234567890" or "71234567890"')
            );
        }

        return "+${matches[1]}";
    }
}