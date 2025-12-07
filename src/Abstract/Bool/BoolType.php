<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Bool;

use PhpTypedValues\Abstract\TypeInterface;

/**
 * @psalm-immutable
 */
abstract class BoolType implements TypeInterface, BoolTypeInterface
{
    abstract protected function __construct(bool $value);

    public function toString(): string
    {
        return $this->value() ? 'true' : 'false';
    }

    /**
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static($value);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
