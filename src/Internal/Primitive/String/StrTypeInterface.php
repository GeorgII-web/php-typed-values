<?php

declare(strict_types=1);

namespace PhpTypedValues\Internal\Primitive\String;

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
    public function value(): string;

    public static function tryFromString(string $value): static|Undefined;

    public static function tryFromMixed(mixed $value): static|Undefined;
}
