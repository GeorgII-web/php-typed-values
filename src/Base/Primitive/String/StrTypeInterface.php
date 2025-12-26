<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\String;

use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Contract for string-typed values.
 *
 * Declares the API for string-backed value objects, including factory
 * methods, accessors, and formatting helpers.
 *
 * Example
 *  - $v = MyString::fromString('abc');
 *  - $v->value(); // 'abc'
 *
 * @psalm-immutable
 */
interface StrTypeInterface
{
    /**
     * Create an instance from a validated string representation.
     *
     * Implementations should perform strict validation and may throw a
     * domain-specific subtype of {@see TypeException}
     * when the provided value is invalid.
     *
     * @throws TypeException
     */
    public static function fromString(string $value): static;

    public function value(): string;

    /**
     * @template T
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromMixed(mixed $value, mixed $default = new Undefined()): mixed;

    /**
     * @template T
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(string $value, mixed $default = new Undefined()): mixed;
}
