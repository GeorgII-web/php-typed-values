<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias;

use PhpTypedValues\String\StringNonEmpty;

/**
 * Alias of Non-empty string value.
 *
 * Example "hello"
 *
 * @psalm-immutable
 */
readonly class NonEmptyStr extends StringNonEmpty
{
}
