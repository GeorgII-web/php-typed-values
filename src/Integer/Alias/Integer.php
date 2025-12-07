<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\Alias;

use PhpTypedValues\Integer\IntegerStandard;

/**
 * Alias for the generic integer-typed value.
 *
 * Provides the same behavior as IntegerStandard while offering a concise
 * descriptive name for APIs that prefer "Integer".
 *
 * Example
 *  - $v = Integer::fromString('7');
 *    $v->toString(); // "7"
 *  - $v = Integer::fromInt(42);
 *    (string) $v; // "42"
 *
 * @psalm-immutable
 */
class Integer extends IntegerStandard
{
}
