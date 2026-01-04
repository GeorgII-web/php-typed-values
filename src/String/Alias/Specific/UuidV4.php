<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Specific;

use PhpTypedValues\Integer\String\Specific\StringUuidV4;

/**
 * Alias for UUID version 4 (random) string.
 *
 * Provides the same behavior as StringUuidV4 while exposing a concise
 * name suitable for APIs that prefer "UuidV4".
 *
 * Example
 *  - $u = UuidV4::fromString('550E8400-E29B-41D4-A716-446655440000');
 *    $u->toString(); // '550e8400-e29b-41d4-a716-446655440000'
 *
 * @psalm-immutable
 */
final readonly class UuidV4 extends StringUuidV4
{
}
