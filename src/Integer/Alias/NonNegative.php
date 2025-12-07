<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\Alias;

use PhpTypedValues\Integer\IntegerNonNegative;

/**
 * Alias for non‑negative integer (>= 0).
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
readonly class NonNegative extends IntegerNonNegative
{
}
