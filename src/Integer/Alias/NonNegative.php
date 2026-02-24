<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\Alias;

use PhpTypedValues\Integer\IntegerNonNegative;

/**
 * NonNegative (Alias for non-negative integer).
 *
 * Provides the same behavior as IntegerNonNegative while exposing a concise
 * name suitable for APIs that prefer "NonNegative".
 *
 * Example
 *  - $v = NonNegative::fromString('0');
 *    $v->value(); // 0
 *  - $v = NonNegative::fromInt(10);
 *    (string) $v; // "10"
 *
 * @psalm-immutable
 */
final class NonNegative extends IntegerNonNegative
{
}
