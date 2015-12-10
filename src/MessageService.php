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

use LitGroup\Sms\Gateway\GatewayInterface;

/**
 * Default implementation of message service.
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
class MessageService implements MessageServiceInterface
{
    /**
     * @var GatewayInterface
     */
    private $gateway;


    /**
     * @param GatewayInterface $gateway
     */
    public function __construct(GatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * {@inheritDoc}
     */
    public function sendMessage(Message $message)
    {
        if ($this->hasEmptyBody($message)) {
            throw new \InvalidArgumentException('A message body cannot be empty or contain spaces only.');
        }

        if ($this->hasNoRecipients($message)) {
            throw new \InvalidArgumentException('A message must have at least one recipient.');
        }

        $this->gateway->sendMessage(
            new Message(
                $message->getBody(),
                $this->filterRecipients($message->getRecipients()),
                $message->getSender()
            )
        );
    }

    /**
     * @param Message $message
     *
     * @return boolean
     */
    private function hasEmptyBody(Message $message)
    {
        return ($message->getBody() === null || trim($message->getBody()) === '');
    }

    /**
     * @param Message $message
     *
     * @return boolean
     */
    private function hasNoRecipients(Message $message)
    {
        return count($message->getRecipients()) === 0;
    }

    /**
     * Normalizes format of each phone number and removes duplications.
     *
     * @param string[] $recipients
     *
     * @return string[]
     *
     * @throws \InvalidArgumentException
     */
    private function filterRecipients(array $recipients)
    {
        $index = [];

        foreach ($recipients as $recipient) {
            if (preg_match('/^\+?(\d+)$/Ds', $recipient, $matches) === 1) {
                $index[$matches[1]] = null;
            } else {
                throw new \InvalidArgumentException(
                    sprintf('Recipient number "%s" has invalid format.', $recipient)
                );
            }
        }

        return array_keys($index);
    }
}