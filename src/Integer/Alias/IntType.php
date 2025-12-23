<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\Alias;

use PhpTypedValues\Integer\IntegerStandard;

/**
 * Alias for the generic integer-typed value.
 *
 * Provides the same behavior as IntegerStandard while offering a more
 * descriptive name for APIs that prefer "IntType".
 *
 * Example
 *  - $v = IntType::fromString('7');
 *    $v->toString(); // "7"
 *  - $v = IntType::fromInt(42);
 *    (string) $v; // "42"
 *
 * @psalm-immutable
 */
final class IntType extends IntegerStandard
{
}
