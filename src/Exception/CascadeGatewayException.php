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
     * @param array $exceptions Map with names of gateways as keys and exceptions as values.
     */
    public function __construct(array $exceptions)
    {
        parent::__construct(
            sprintf(
                'All SMS gateways are inoperative (%d gateways total).',
                count($exceptions)
            )
        );
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
        $lines = [
            parent::__toString()
        ];

        foreach ($this->cascadeExceptions as $gatewayName => $exception) {
            array_push(
                $lines,
                $this->formatInnerException($gatewayName, $exception)
            );
        }

        return implode("\n\n", $lines);
    }

    /**
     * @param string $gatewayName
     * @param GatewayException $exception
     *
     * @return string
     */
    private function formatInnerException($gatewayName, GatewayException $exception)
    {
        return sprintf("==> \"Gateway %s:\"\n%s", $gatewayName, (string) $exception);
    }
}