<?php

declare(strict_types=1);

namespace PhpTypedValues\Float;

use PhpTypedValues\Code\Exception\NumericTypeException;
use PhpTypedValues\Code\Float\FloatType;

/**
 * Represents any PHP float (double).
 *
 * @psalm-immutable
 */
readonly class FloatBasic extends FloatType
{
    protected float $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

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
