<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\Alias;

use PhpTypedValues\Integer\IntegerPositive;

/**
 * ID (Alias for positive integer).
 *
 * Provides the same behavior as IntegerPositive but conveys the semantic
 * meaning of an application-level identifier. Useful where IDs are strictly
 * positive integers.
 *
 * Example
 *  - $id = Id::fromString('42');
 *    $id->value(); // 42
 *  - $id = Id::fromInt(7);
 *    (string) $id; // "7"
 *
 * @psalm-immutable
 */
final readonly class Id extends IntegerPositive
{
}
