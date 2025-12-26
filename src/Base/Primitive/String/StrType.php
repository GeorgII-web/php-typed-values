<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\String;

use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Base implementation for string-typed values.
 *
 * Provides common formatting helpers for value objects backed by strings.
 * Concrete string types extend this class and add domain-specific
 * validation/normalization.
 *
 * Example
 *  - $v = MyString::fromString('hello');
 *  - $v->toString(); // "hello"
 *
 * @psalm-immutable
 */
abstract readonly class StrType extends PrimitiveType implements StrTypeInterface
{
    /**
     * @template T
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromMixed(mixed $value, mixed $default = new Undefined()): mixed
    {
        try {
            $instance = static::fromString(
                static::convertMixedToString($value)
            );

            /** @var static|T */
            return $instance;
        } catch (TypeException) {
            return $default;
        }
    }

    /**
     * @template T
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(string $value, mixed $default = new Undefined()): mixed
    {
        try {
            $instance = static::fromString($value);

            /** @var static|T */
            return $instance;
        } catch (TypeException) {
            return $default;
        }
    }
}
