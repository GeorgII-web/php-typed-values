<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\Alias;

use PhpTypedValues\Integer\IntegerPositive;

/**
 * Positive (Alias for positive integer).
 *
 * Provides the same behavior as IntegerPositive while exposing a concise
 * name suitable for APIs that prefer "Positive".
 *
 * Example
 *  - $v = Positive::fromString('1');
 *    $v->value(); // 1
 *  - $v = Positive::fromInt(5);
 *    (string) $v; // "5"
 *
 * @psalm-immutable
 */
final class Positive extends IntegerPositive
{
}
