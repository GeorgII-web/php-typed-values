<?php

declare(strict_types=1);

namespace PhpTypedValues\Float;

use PhpTypedValues\Abstract\Float\FloatType;
use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Represents any PHP float (double).
 *
 * Example "3.14"
 *
 * @psalm-immutable
 */
readonly class FloatStandard extends FloatType
{
    protected float $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

    public static function fromFloat(float $value): static
    {
        return new static($value);
    }

    public static function tryFromString(string $value): static|Undefined
    {
        try {
            return static::fromString($value);
        } catch (TypeException) {
            return Undefined::create();
        }
    }

    public static function tryFromFloat(float $value): static|Undefined
    {
        return static::fromFloat($value);
    }

    /**
     * @throws FloatTypeException
     */
    public static function fromString(string $value): static
    {
        parent::assertFloatString($value);

        return new static((float) $value);
    }

    public function value(): float
    {
        return $this->value;
    }
}
