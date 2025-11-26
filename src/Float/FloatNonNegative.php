<?php

declare(strict_types=1);

namespace PhpTypedValues\Float;

use PhpTypedValues\Abstract\Float\FloatType;
use PhpTypedValues\Exception\FloatTypeException;

use function sprintf;

/**
 * Non-negative float (>= 0.0).
 *
 * Example "0.0"
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
            throw new FloatTypeException(sprintf('Expected non-negative float, got "%d"', $value));
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
}
