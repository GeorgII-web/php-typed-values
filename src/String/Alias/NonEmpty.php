<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias;

use PhpTypedValues\String\StringNonEmpty;

/**
 * Alias for non-empty string typed value.
 *
 * Provides the same behavior as StringNonEmpty while exposing a concise
 * name suitable for APIs that prefer "NonEmpty".
 *
 * Example
 *  - $v = NonEmpty::fromString('hello');
 *    $v->value(); // 'hello'
 *
 * @method        false            isUndefined()
 * @method        non-empty-string value()
 * @method        bool             isEmpty()
 * @method static static|mixed     tryFromString(string $value, mixed $default = null)
 * @method static static|mixed     tryFromMixed(mixed $value, mixed $default = null)
 *
 * @psalm-immutable
 */
final readonly class NonEmpty extends StringNonEmpty
{
}
