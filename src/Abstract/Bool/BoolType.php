<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Bool;

/**
 * @psalm-immutable
 */
abstract class BoolType implements BoolTypeInterface
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
}
