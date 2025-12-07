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
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value)
    {
        try {
            return static::fromString($value);
        } catch (TypeException $exception) {
            return Undefined::create();
        }
    }

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromFloat(float $value)
    {
        return static::fromFloat($value);
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
