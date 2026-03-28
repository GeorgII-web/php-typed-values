<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Specific;

use PhpTypedValues\String\Specific\StringHex;

/**
 * Alias for hexadecimal string.
 *
 * Provides the same behavior as StringHex while exposing a concise
 * name suitable for APIs that prefer "Hex".
 *
 * Example
 *  - $h = Hex::fromString('4a6f686e');
 *    $h->toString(); // "4a6f686e"
 *
 * @psalm-immutable
 */
final readonly class Hex extends StringHex
{
}
