<?php

declare(strict_types=1);

namespace PhpTypedValues\Float;

use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Internal\Primitive\Float\FloatType;
use PhpTypedValues\Undefined\Alias\Undefined;

use function sprintf;

/**
 * Non‑negative float-typed value (>= 0.0).
 *
 * Guarantees the wrapped float is zero or positive. Offers factories from
 * validated string and native float, plus standard string formatting.
 *
 * Example
 *  - $v = FloatNonNegative::fromString('0.0');
 *    $v->value(); // 0.0
 *  - $v = FloatNonNegative::fromFloat(10.25);
 *    (string) $v; // "10.25"
 *
 * @psalm-immutable
 */
readonly class FloatNonNegative extends FloatType
{
    protected float $value;

    /**
     * @throws FloatTypeException
     */
    public function __construct(float $value)
    {
        if ($value < 0) {
            throw new FloatTypeException(sprintf('Expected non-negative float, got "%s"', $value));
        }

        $this->value = $value;
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

    /**
     * @throws FloatTypeException
     */
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
        try {
            return static::fromFloat($value);
        } catch (TypeException) {
            return Undefined::create();
        }
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
}
