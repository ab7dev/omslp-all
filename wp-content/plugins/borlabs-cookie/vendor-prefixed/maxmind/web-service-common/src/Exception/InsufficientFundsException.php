<?php
/**
 * @license Apache-2.0
 *
 * Modified by borlabs on 28-April-2026 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace Borlabs\Cookie\Dependencies\MaxMind\Exception;

/**
 * Thrown when the account is out of credits.
 */
class InsufficientFundsException extends InvalidRequestException
{
}
