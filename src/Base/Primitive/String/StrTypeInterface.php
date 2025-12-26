<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\String;

use PhpTypedValues\Base\Primitive\PrimitiveTypeInterface;

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
interface StrTypeInterface extends PrimitiveTypeInterface
{
    public function value(): string;

    public function jsonSerialize(): mixed;
}
