<?php

declare(strict_types=1);

namespace PhpTypedValues\Float;

use PhpTypedValues\Abstract\Float\FloatType;
use PhpTypedValues\Exception\FloatTypeException;

/**
 * Represents any PHP float (double).
 *
 * Example "3.14"
 *
 * @psalm-immutable
 */
class FloatStandard extends FloatType
{
    /**
     * @readonly
     */
    protected float $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

    /**
     * @return static
     */
    public static function fromFloat(float $value)
    {
        return new static($value);
    }

    /**
     * @throws FloatTypeException
     * @return static
     */
    public static function fromString(string $value)
    {
        parent::assertFloatString($value);

        return new static((float) $value);
    }

    public function value(): float
    {
        return $this->value;
    }
}
