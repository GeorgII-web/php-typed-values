<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Bool;

use PhpTypedValues\Abstract\TypeInterface;

/**
 * @psalm-immutable
 */
abstract readonly class BoolType implements TypeInterface, BoolTypeInterface
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
