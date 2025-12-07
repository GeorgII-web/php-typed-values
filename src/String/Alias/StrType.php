<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias;

use PhpTypedValues\String\StringStandard;

/**
 * Alias for the generic string typed value ("StrType").
 *
 * Provides the same behavior as StringStandard while exposing a more
 * descriptive name for APIs that prefer "StrType".
 *
 * Example
 *  - $v = StrType::fromString('x');
 *    $v->toString(); // "x"
 *
 * @psalm-immutable
 */
class StrType extends StringStandard
{
}
