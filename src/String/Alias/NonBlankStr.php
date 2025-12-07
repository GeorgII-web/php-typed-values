<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias;

use PhpTypedValues\String\StringNonBlank;

/**
 * Alias of Non-blank string value.
 *
 * Example "hello", blank string like " " will fail.
 *
 * @psalm-immutable
 */
readonly class NonBlankStr extends StringNonBlank
{
}
