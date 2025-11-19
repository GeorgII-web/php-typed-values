<?php

declare(strict_types=1);

namespace PhpTypedValues\Float;

use PhpTypedValues\Code\Assert\Assert;
use PhpTypedValues\Code\Exception\NumericTypeException;
use PhpTypedValues\Code\Float\FloatType;

/**
 * @psalm-immutable
 */
final readonly class NonNegativeFloat extends FloatType
{
    protected float $value;

    /**
     * @throws NumericTypeException
     */
    public function __construct(float $value)
    {
        Assert::greaterThanEq($value, 0);

        $this->value = $value;
    }

    /**
     * @throws NumericTypeException
     */
    public static function fromFloat(float $value): self
    {
        return new self($value);
    }

    /**
     * @throws NumericTypeException
     */
    public static function fromString(string $value): self
    {
        parent::assertNumericString($value);

        return new self((float) $value);
    }

    public function value(): float
    {
        return $this->value;
    }
}
