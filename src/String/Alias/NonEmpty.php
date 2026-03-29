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
 * @psalm-immutable
 */
final class NonEmpty extends StringNonEmpty
{
}
