<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\Alias;

use PhpTypedValues\Integer\IntegerStandard;

/**
 * IntegerType (Alias for integer).
 *
 * Provides the same behavior as IntegerStandard while offering a more
 * descriptive name for APIs that prefer "IntegerType".
 *
 * Example
 *  - $v = IntegerType::fromString('7');
 *    $v->toString(); // "7"
 *  - $v = IntegerType::fromInt(42);
 *    (string) $v; // "42"
 *
 * @psalm-immutable
 */
final class IntegerType extends IntegerStandard
{
}
