<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LitGroup\Sms\Exception;

/**
 * ChainGatewayException
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
class CascadeGatewayException extends GatewayException
{
    private $cascadeExceptions = [];

    /**
     * CascadeGatewayException constructor.
     *
     * @param array $exceptions ['gateway_name' => $exception]
     */
    public function __construct(array $exceptions)
    {
        parent::__construct(sprintf('No one gateway is available (%d gateways failed).', count($exceptions)));

        $this->cascadeExceptions = $exceptions;
    }

    /**
     * Returns cascade exceptions.
     *
     * @return array Like ['gateway_name' => $exception].
     */
    public function getCascadeExceptions()
    {
        return $this->cascadeExceptions;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $lines = [];

        foreach ($this->cascadeExceptions as $gatewayName => $exception) {
            array_push(
                $lines,
                sprintf("### Gateway %s:\n\n%s", $gatewayName, (string) $exception)
            );
        }

        return implode("\n\n\n", $lines);
    }
}