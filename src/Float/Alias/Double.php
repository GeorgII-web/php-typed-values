<?php

declare(strict_types=1);

namespace PhpTypedValues\Float\Alias;

use PhpTypedValues\Float\FloatStandard;

/**
 * Alias for the generic float-typed value ("double").
 *
 * Provides the same behavior as FloatStandard while offering a commonly used
 * synonym name "Double".
 *
 * Example
 *  - $v = Double::fromString('2.5');
 *    $v->toString(); // "2.5"
 *  - $v = Double::fromFloat(0.75);
 *    (string) $v; // "0.75"
 *
 * @psalm-immutable
 */
final class Double extends FloatStandard
{
}
