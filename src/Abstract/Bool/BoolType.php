<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Bool;

use PhpTypedValues\Abstract\AbstractType;

/**
 * Base implementation for boolean typed values.
 *
 * Provides common formatting helpers and factory methods for bool-backed
 * value objects. Concrete boolean types extend this class and add
 * domain-specific validation if needed.
 *
 * Example
 *  - $v = MyBoolean::fromBool(true);
 *  - $v->toString(); // "true"
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract readonly class BoolType extends AbstractType implements BoolTypeInterface
{
    abstract protected function __construct(bool $value);

    public function toString(): string
    {
        return $this->value() ? 'true' : 'false';
    }

    public static function fromBool(bool $value): static
    {
        return new static($value);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
