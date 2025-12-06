<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Bool;

/**
 * @psalm-immutable
 */
abstract readonly class BoolType implements BoolTypeInterface
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
}
