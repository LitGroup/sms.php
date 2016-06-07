<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LitGroup\Sms\Gateway;

use LitGroup\Sms\Exception\CascadeGatewayException;
use LitGroup\Sms\Exception\GatewayException;
use LitGroup\Sms\Message;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Cascade gateway.
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
class CascadeGateway implements GatewayInterface
{
    /**
     * @var array
     */
    private $gateways;

    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * CascadeGateway constructor.
     *
     * @param array $gateways Map of gateways with name of gateway as key.
     * @param LoggerInterface|null $logger
     */
    public function __construct(array $gateways, LoggerInterface $logger = null)
    {
        $this->setGateways($gateways);
        $this->setLogger($logger);
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
     * @param array $gateways
     *
     * @return void
     */
    private function setGateways(array $gateways)
    {
        $this->gateways = [];
        foreach ($gateways as $name => $gateway) {
            $this->addGateway($name, $gateway);
        }
    }

    /**
     * @param string $name
     * @param GatewayInterface $gateway
     *
     * @return void
     */
    private function addGateway($name, GatewayInterface $gateway)
    {
        $this->gateways[$name] = $gateway;
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