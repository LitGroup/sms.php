<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests\LitGroup\Sms\Log;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class TestLogger extends AbstractLogger
{
    /**
     * @var array
     */
    private $logs = [];

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context =[])
    {
        if (!array_key_exists($level, $this->logs)) {
            $this->logs[$level] = [];
        }

        array_push($this->logs[$level], [
            'message' => $message,
            'context' => $context
        ]);
    }

    /**
     * @param mixed $level
     *
     * @return array[]
     */
    public function getLogs($level)
    {
        if (!array_key_exists($level, $this->logs)) {
            return [];
        }

        return $this->logs[$level];
    }

    /**
     * @return array[]
     */
    public function getAlerts()
    {
        return $this->getLogs(LogLevel::ALERT);
    }

    /**
     * @return array[]
     */
    public function getWarnings()
    {
        return $this->getLogs(LogLevel::WARNING);
    }

    /**
     * @return array[]
     */
    public function getInfos()
    {
        return $this->getLogs(LogLevel::INFO);
    }

}