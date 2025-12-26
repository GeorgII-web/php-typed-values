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
 * @method        false        isUndefined()
 * @method        string       value()
 * @method        bool         isEmpty()
 * @method static static|mixed tryFromString(string $value, mixed $default = null)
 * @method static static|mixed tryFromMixed(mixed $value, mixed $default = null)
 *
 * @psalm-immutable
 */
final readonly class StrType extends StringStandard
{
}
