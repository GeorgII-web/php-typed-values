<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\Alias;

use PhpTypedValues\Integer\IntegerNonNegative;

/**
 * Alias of Non-negative integer (>= 0).
 *
 * Example "0"
 *
 * @psalm-immutable
 */
readonly class NonNegative extends IntegerNonNegative
{
}
