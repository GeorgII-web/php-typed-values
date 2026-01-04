<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias;

use PhpTypedValues\Integer\String\StringEmpty;

/**
 * Alias for an empty string typed value.
 *
 * Provides the same behavior as StringEmpty
 *
 * Example
 *  - $v = EmptyStr::fromString('');
 *    $v->value(); // ''
 *
 * @psalm-immutable
 */
final readonly class EmptyStr extends StringEmpty
{
}
