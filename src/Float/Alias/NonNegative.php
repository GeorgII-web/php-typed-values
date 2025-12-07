<?php

declare(strict_types=1);

namespace PhpTypedValues\Float\Alias;

use PhpTypedValues\Float\FloatNonNegative;

/**
 * Alias of Non-negative float (>= 0.0).
 *
 * Example "0.0"
 *
 * @psalm-immutable
 */
class NonNegative extends FloatNonNegative
{
}
