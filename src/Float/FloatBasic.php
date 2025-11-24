<?php

declare(strict_types=1);

namespace PhpTypedValues\Float;

use PhpTypedValues\Code\Exception\FloatTypeException;
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
