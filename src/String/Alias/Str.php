<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias;

use PhpTypedValues\String\StringStandard;

/**
 * Alias for the generic string typed value.
 *
 * Provides the same behavior as StringStandard while exposing a concise
 * name suitable for APIs that prefer "Str".
 *
 * Example
 *  - $s = Str::fromString('hello');
 *    $s->toString(); // "hello"
 *
 * @psalm-immutable
 */
readonly class Str extends StringStandard
{
}
