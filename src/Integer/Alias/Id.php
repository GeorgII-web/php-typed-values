<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\Alias;

use PhpTypedValues\Integer\IntegerPositive;

/**
 * Alias of Positive integer used as identifier.
 *
 * Example "42"
 *
 * @psalm-immutable
 */
readonly class Id extends IntegerPositive
{
}
