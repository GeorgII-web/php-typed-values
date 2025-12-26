<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\String;

use PhpTypedValues\Base\Primitive\PrimitiveTypeInterface;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

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
interface StrTypeInterface extends PrimitiveTypeInterface, Stringable
{
    public function value(): string;

    public function jsonSerialize(): mixed;

    public function toString(): string;

    public function __toString(): string;

    public function isEmpty(): bool;

    public function isUndefined(): false;

    /**
     * @template T
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(string $value, mixed $default = new Undefined()): mixed;

    /**
     * @template T
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromMixed(mixed $value, mixed $default = new Undefined()): mixed;
}
