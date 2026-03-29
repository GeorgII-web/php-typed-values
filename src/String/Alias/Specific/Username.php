<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Specific;

use PhpTypedValues\String\Specific\StringUsername;

/**
 * Alias for Username string.
 *
 * Provides the same behavior as StringUsername while exposing a concise
 * name suitable for APIs that prefer "Username".
 *
 * Example
 *  - $u = Username::fromString('john_doe');
 *    $u->toString(); // "john_doe"
 *
 * @psalm-immutable
 */
final readonly class Username extends StringUsername
{
}
