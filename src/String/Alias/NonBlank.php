<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias;

use PhpTypedValues\String\StringNonBlank;

/**
 * Alias for non‑blank string typed value.
 *
 * Provides the same behavior as StringNonBlank while exposing a concise
 * name suitable for APIs that prefer "NonBlank".
 *
 * Example
 *  - $v = NonBlank::fromString(' hello ');
 *    $v->toString(); // ' hello '
 *
 * @method        false            isUndefined()
 * @method        non-empty-string value()
 * @method        bool             isEmpty()
 * @method        string           toString()
 * @method static static|mixed     tryFromString(string $value, mixed $default = null)
 * @method static static|mixed     tryFromMixed(mixed $value, mixed $default = null)
 *
 * @psalm-immutable
 */
final readonly class NonBlank extends StringNonBlank
{
}
