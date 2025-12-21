<?php

declare(strict_types=1);

namespace PhpTypedValues\Float;

use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Internal\Primitive\Float\FloatType;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Generic float-typed value.
 *
 * Wraps any PHP float (double) and provides factories from native float or
 * validated string, along with convenient string formatting.
 *
 * Example
 *  - $v = FloatStandard::fromString('3.14');
 *    $v->value(); // 3.14 (float)
 *  - $v = FloatStandard::fromFloat(0.5);
 *    (string) $v; // "0.5"
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

    public static function tryFromMixed(mixed $value): static|Undefined
    {
        try {
            return static::fromString(
                static::convertMixedToString($value)
            );
        } catch (TypeException) {
            return Undefined::create();
        }
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
     * String conversion uses a setting called serialize_precision (usually 14 or 17, but often configured to round the last digit for "cleaner" output)
     *
     * @throws FloatTypeException
     */
    public static function fromString(string $value): static
    {
        parent::assertFloatString($value);

        return new static((float) (string) (float) $value);
    }

    public function value(): float
    {
        return $this->value;
    }

    public function jsonSerialize(): float
    {
        return $this->value();
    }

    public function toString(): string
    {
        return (string) $this->value();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }
}
