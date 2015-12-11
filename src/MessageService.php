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

use LitGroup\Sms\Exception\GatewayException;
use LitGroup\Sms\Gateway\GatewayInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Default implementation of message service.
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
class MessageService implements MessageServiceInterface, LoggerAwareInterface
{
    /**
     * @var GatewayInterface
     */
    private $gateway;

    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * @param GatewayInterface $gateway
     */
    public function __construct(GatewayInterface $gateway)
    {
        $this->gateway = $gateway;
        $this->logger = new NullLogger();
    }

    /**
     * @inheritDoc
     */
    public function createMessage()
    {
        return new Message();
    }

    /**
     * @inheritDoc
     */
    public function sendMessage(Message $message)
    {
        $this->validateMessage($message);

        try {
            $this->gateway->sendMessage($message);

            $this->logger->info('[SMS] Message was sent.', [
                'message' => [
                    'body'       => $message->getBody(),
                    'recipients' => $message->getRecipients(),
                    'sender'     => $message->getSender(),
                ]
            ]);
        } catch (GatewayException $e) {
            $this->logger->alert('[SMS] SMS Gateway problem occurred.', [
                'exception' => $e
            ]);

            throw $e;
        }
    }

    /**
     * @param Message $message
     *
     * @throws \InvalidArgumentException
     */
    private function validateMessage(Message $message)
    {
        if ($message->getBody() === null || trim($message->getBody()) === '') {
            throw new \InvalidArgumentException('Message body cannot be empty or contain spaces only.');
        }

        if (count($message->getRecipients()) === 0) {
            throw new \InvalidArgumentException('At least one recipient should be given.');
        }
    }

    /**
     * @inheritDoc
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}