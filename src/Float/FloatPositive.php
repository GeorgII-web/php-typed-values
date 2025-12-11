<?php

declare(strict_types=1);

namespace PhpTypedValues\Float;

use PhpTypedValues\Abstract\Float\FloatType;
use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

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
class FloatPositive extends FloatType
{
    /**
     * @readonly
     */
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
     * @param mixed $value
     */
    public static function tryFromMixed($value)
    {
        try {
            return static::fromString((string) $value);
        } catch (TypeException $exception) {
            return Undefined::create();
        }
    }

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromFloat(float $value)
    {
        try {
            return static::fromFloat($value);
        } catch (TypeException $exception) {
            return Undefined::create();
        }
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

    public function jsonSerialize(): float
    {
        return $this->value();
    }
}
