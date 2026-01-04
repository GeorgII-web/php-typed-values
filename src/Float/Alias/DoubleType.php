<?php

declare(strict_types=1);

namespace PhpTypedValues\Float\Alias;

use PhpTypedValues\Float\FloatStandard;

/**
 * Alias for the generic float-typed value.
 *
 * Provides the same behavior as FloatStandard while offering a more descriptive
 * name for APIs that prefer "DoubleType".
 *
 * Example
 *  - $v = DoubleType::fromString('1.25');
 *    $v->toString(); // "1.25"
 *
 * @psalm-immutable
 */
final readonly class DoubleType extends FloatStandard
{
}
