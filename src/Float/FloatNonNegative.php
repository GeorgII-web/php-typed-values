<?php

declare(strict_types=1);

namespace PhpTypedValues\Float;

use PhpTypedValues\Base\Primitive\Float\FloatType;
use PhpTypedValues\Exception\FloatTypeException;

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

        if (is_infinite($value)) {
            throw new FloatTypeException('Infinite float value');
        }

        if (is_nan($value)) {
            throw new FloatTypeException('Not a number float value');
        }

        $this->value = $value;
    }

    /**
     * @throws FloatTypeException
     */
    public static function fromFloat(float $value): static
    {
        return new static($value);
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
        return $this->value;
    }

    public function toString(): string
    {
        return (string) $this->value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
