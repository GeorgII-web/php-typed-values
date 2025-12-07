<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\String;

use PhpTypedValues\Abstract\TypeInterface;

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
abstract readonly class StrType implements TypeInterface, StrTypeInterface
{
    public function toString(): string
    {
        return $this->value();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
