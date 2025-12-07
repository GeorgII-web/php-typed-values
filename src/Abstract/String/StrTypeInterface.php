<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\String;

use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Contract for string-typed values.
 *
 * Declares the API for string-backed value objects, including factory
 * methods, accessors and formatting helpers.
 *
 * Example
 *  - $v = MyString::fromString('abc');
 *  - $v->value(); // 'abc'
 *
 * @psalm-immutable
 */
interface StrTypeInterface
{
    public function value(): string;

    /**
     * @return static
     */
    public static function fromString(string $value);

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value);

    public function toString(): string;

    public function __toString(): string;
}
