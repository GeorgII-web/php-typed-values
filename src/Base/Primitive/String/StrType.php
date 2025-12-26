<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\String;

use PhpTypedValues\Base\Primitive\PrimitiveType;
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
 * @internal
 *
 * @psalm-internal PhpTypedValues
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
    abstract public static function tryFromMixed(mixed $value, mixed $default = new Undefined()): mixed;

    /**
     * @template T
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromString(string $value, mixed $default = new Undefined()): mixed;
}
