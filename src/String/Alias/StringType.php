<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias;

use PhpTypedValues\String\StringStandard;

/**
 * Alias for the generic string typed value ("StringType").
 *
 * Provides the same behavior as StringStandard while exposing a more
 * descriptive name for APIs that prefer "StringType".
 *
 * Example
 *  - $v = StringType::fromString('x');
 *    $v->toString(); // "x"
 *
 * @psalm-immutable
 */
final readonly class StringType extends StringStandard
{
}
