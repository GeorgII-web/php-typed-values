<?php

declare(strict_types=1);

namespace PhpTypedValues\Float\Alias;

use PhpTypedValues\Float\FloatPositive;

/**
 * Alias for positive float-typed value (> 0.0).
 *
 * Provides the same behavior as FloatPositive while exposing a concise name
 * suitable for APIs that prefer "Positive".
 *
 * Example
 *  - $v = Positive::fromString('0.1');
 *    $v->value(); // 0.1
 *  - $v = Positive::fromFloat(2.5);
 *    (string) $v; // "2.5"
 *
 * @psalm-immutable
 */
class Positive extends FloatPositive
{
}
