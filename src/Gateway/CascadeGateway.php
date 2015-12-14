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

use LitGroup\Sms\Exception\CascadeGatewayException;
use LitGroup\Sms\Exception\GatewayException;
use LitGroup\Sms\Message;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Cascade gateway.
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
class CascadeGateway implements GatewayInterface, LoggerAwareInterface
{
    /**
     * @var array
     */
    private $gateways = [];

    /**
     * @var LoggerInterface
     */
    private $logger;


    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    /**
     * Sends message.
     *
     * @param Message $message It is expected that the facility has verified and contains correct information.
     *
     * @return void
     *
     * @throws CascadeGatewayException
     */
    public function sendMessage(Message $message)
    {
        $exceptions = [];

        /** @var string $gatewayName */
        /** @var GatewayInterface $gateway*/
        foreach ($this->gateways as $gatewayName => $gateway) {
            try {
                $gateway->sendMessage($message);

                return;
            } catch (GatewayException $e) {
                $exceptions[$gatewayName] = $e;

                $this->logger->warning(
                    sprintf('(Cascade gateway: %s) %s', $gatewayName, $e->getMessage()),
                    ['exception' => $e]
                );
            }
        }

        throw new CascadeGatewayException($exceptions);
    }

    /**
     * Adds gateway.
     *
     * @param string           $name    Name of gateway will be used for logging of errors.
     * @param GatewayInterface $gateway
     *
     * @return $this
     */
    public function addGateway($name, GatewayInterface $gateway)
    {
        $name = strtolower($name);
        if (array_key_exists($name, $this->gateways)) {
            throw new \InvalidArgumentException(
                sprintf('Gateway with name "%s" already registered in a CascadeGateway.', $name)
            );
        }

        $this->gateways[$name] = $gateway;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}