<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias;

use PhpTypedValues\Exception\String\StringTypeException;
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
final class StringType extends StringStandard
{
    /**
     * @throws StringTypeException
     * @return never
     */
    public static function fromNull(null $value)
    {
        throw new StringTypeException('StringType type cannot be created from null');
    }

    /**
     * @throws StringTypeException
     * @return never
     */
    public static function toNull()
    {
        throw new StringTypeException('StringType type cannot be converted to null');
    }
}
