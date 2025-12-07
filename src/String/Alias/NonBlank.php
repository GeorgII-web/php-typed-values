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
 * @psalm-immutable
 */
class NonBlank extends StringNonBlank
{
}
