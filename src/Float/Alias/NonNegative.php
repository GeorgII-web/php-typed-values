<?php

declare(strict_types=1);

namespace PhpTypedValues\Float\Alias;

use PhpTypedValues\Float\FloatNonNegative;

/**
 * Alias for non‑negative float-typed value (>= 0.0).
 *
 * Provides the same behavior as FloatNonNegative while exposing a concise
 * name suitable for APIs that prefer "NonNegative".
 *
 * Example
 *  - $v = NonNegative::fromString('0.0');
 *    $v->value(); // 0.0
 *  - $v = NonNegative::fromFloat(10.25);
 *    (string) $v; // "10.25"
 *
 * @psalm-immutable
 */
class NonNegative extends FloatNonNegative
{
}
