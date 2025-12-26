<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\String;

use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Base\Shared\FromStringInterface;
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
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract readonly class StrType extends PrimitiveType implements StrTypeInterface, FromStringInterface
{
    protected string $value;

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
            /** @var static */
            return static::fromString($value);
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
    public static function tryFromMixed(mixed $value, mixed $default = new Undefined()): mixed
    {
        try {
            /** @var static */
            return static::fromString(
                static::convertMixedToString($value)
            );
        } catch (TypeException) {
            return $default;
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return $this->value === '';
    }

    public function isUndefined(): false
    {
        return false;
    }
}
