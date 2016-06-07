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
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Default implementation of message service.
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 *
 * @api
 */
class MessageService implements MessageServiceInterface
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
     * @param LoggerInterface|null $logger
     */
    public function __construct(GatewayInterface $gateway, LoggerInterface $logger = null)
    {
        $this->gateway = $gateway;
        $this->setLogger($logger);
    }

    /**
     * @inheritDoc
     */
    public function sendMessage(Message $message)
    {
        try {
            $this->gateway->sendMessage($message);
        } catch (GatewayException $e) {
            $this->logger->alert('Problem with SMS Gateway has occurred.', [
                'exception' => $e
            ]);

            throw $e;
        }
    }

    /**
     * @param LoggerInterface|null $logger
     *
     * @return void
     */
    private function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger !== null ? $logger : new NullLogger();
    }
}