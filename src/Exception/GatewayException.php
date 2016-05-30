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
 * Class GatewayException
 *
 * @author Roman Shamritskiy <roman@litgroup.r u>
 */
abstract class GatewayException extends \RuntimeException implements SmsExceptionInterface
{
}