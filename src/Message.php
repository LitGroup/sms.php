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

use LitGroup\Sms\Exception\InvalidArgumentException;

/**
 * Message.
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 *
 * @api
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
     * @param string[] $recipients List of phone numbers of recipients. Phone number should be in the format
     *                             "+7000123456789" or "7000123456789".
     * @param null $sender Identifier of sender. (For example "MyCompany"). Identifier should be allowed by
     *                     SMS-Gateway provider.
     */
    public function __construct($body, array $recipients, $sender = null)
    {
        $this->setBody($body);
        $this->setRecipients($recipients);
        $this->setSender($sender);
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
     * Returns the length of the message body in a number of characters.
     *
     * @return integer
     */
    public function getLength()
    {
        return mb_strlen($this->getBody(), 'UTF-8');
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
     * Returns the name of sender.
     *
     * @return string|null
     */
    public function getSender()
    {
        return $this->sender;
    }

    /*
     * @param string $body
     */
    private function setBody($body)
    {
        if (is_null($body)) {
            throw new InvalidArgumentException('Body of message cannot be null');
        }

        if (!is_string($body)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Body of message must be a string, but "%s" given',
                    is_object($body) ? get_class($body) : gettype($body)
                )
            );
        }

        if (mb_strlen($body, 'UTF-8') === 0) {
            throw new InvalidArgumentException('Body of message cannot be an empty string');
        }

        $this->body = (string) $body;
    }

    /**
     * Sets/replaces list of recipients.
     *
     * @param string[] $recipients A list of phone numbers in the one of formats: "+71234567890" or "71234567890".
     *
     * @throws \InvalidArgumentException If one of recipients is not a string or format is invalid.
     */
    private function setRecipients(array $recipients)
    {
        if (count($recipients) === 0) {
            throw new InvalidArgumentException('At least one recipient required');
        }

        $this->recipients = [];
        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }
    }

    /**
     * @param string $recipient A phone number in the one of formats: "+71234567890" or "71234567890".
     *
     * @throws \InvalidArgumentException If $recipient is not a string or format is invalid.
     */
    private function addRecipient($recipient)
    {
        if (is_null($recipient)) {
            throw new InvalidArgumentException('Recipient cannot be NULL');
        }

        if (!is_string($recipient)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Recipient must be presented as string, but "%s" given',
                    is_object($recipient) ? get_class($recipient) : gettype($recipient)
                )
            );
        }

        if (\preg_match('/^\+\d+$/Ds', $recipient) != 1) {
            throw new InvalidArgumentException(
                sprintf(
                    'Recipient must match pattern \+\d+, but "%s" does not match',
                    $recipient
                )
            );
        }

        array_push($this->recipients, $recipient);
    }

    /**
     * @param string $sender
     */
    private function setSender($sender)
    {
        if (is_null($sender)) {
            return;
        }

        if (!is_string($sender)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Sender must be presented as string, but "%s" given',
                    is_object($sender) ? get_class($sender) : gettype($sender)
                )
            );
        }

        if (mb_strlen(trim($sender), 'UTF-8') === 0) {
            throw new InvalidArgumentException('Sender cannot be an empty string or contains whitespaces only');
        }

        $this->sender = $sender;
    }
}