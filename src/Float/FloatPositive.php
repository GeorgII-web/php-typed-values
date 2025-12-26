<?php

declare(strict_types=1);

namespace PhpTypedValues\Float;

use PhpTypedValues\Base\Primitive\Float\FloatType;
use PhpTypedValues\Exception\FloatTypeException;

use function sprintf;

/**
 * Positive float-typed value (> 0.0).
 *
 * Ensures the wrapped float is strictly greater than zero. Provides factories
 * from validated string and native float and convenient formatting helpers.
 *
 * Example
 *  - $v = FloatPositive::fromString('0.1');
 *    $v->value(); // 0.1
 *  - $v = FloatPositive::fromFloat(2.5);
 *    (string) $v; // "2.5"
 *
 * @psalm-immutable
 */
readonly class FloatPositive extends FloatType
{
    protected float $value;

    /**
     * @throws FloatTypeException
     */
    public function __construct(float $value)
    {
        if ($value <= 0.0) {
            throw new FloatTypeException(sprintf('Expected positive float, got "%s"', $value));
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
