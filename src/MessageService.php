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
    public function sendMessage(Message $message)
    {
        try {
            $this->gateway->sendMessage($message);

            $this->logger->info('Message (SMS) was sent.', [
                'message' => [
                    'body'       => $message->getBody(),
                    'recipients' => $message->getRecipients(),
                    'sender'     => $message->getSender(),
                ]
            ]);
        } catch (GatewayException $e) {
            $this->logger->alert('SMS Gateway problem occurred.', [
                'exception' => $e
            ]);

            throw $e;
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