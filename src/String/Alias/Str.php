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
 * @method        false        isUndefined()
 * @method        string       value()
 * @method        bool         isEmpty()
 * @method static static|mixed tryFromString(string $value, mixed $default = null)
 * @method static static|mixed tryFromMixed(mixed $value, mixed $default = null)
 *
 * @psalm-immutable
 */
final readonly class Str extends StringStandard
{
}
