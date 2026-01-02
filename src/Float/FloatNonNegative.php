<?php

declare(strict_types=1);

namespace PhpTypedValues\Float;

use Exception;
use PhpTypedValues\Base\Primitive\Float\FloatType;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
use function is_int;
use function is_string;
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
class FloatNonNegative extends FloatType
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

    public function jsonSerialize(): float
    {
        return $this->value;
    }

    public function toString(): string
    {
        return (string) $this->value;
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromFloat(
        float $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromFloat($value);
        } catch (Exception $exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     * @param mixed $value
     */
    public static function tryFromMixed(
        $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        try {
            switch (true) {
                case is_float($value):
                case is_int($value):
                    return static::fromFloat($value);
                case $value instanceof self:
                    return static::fromFloat($value->value());
                case is_bool($value):
                    return static::fromFloat($value ? 1.0 : 0.0);
                case is_string($value) || is_object($value) && method_exists($value, '__toString'):
                    return static::fromString((string) $value);
                default:
                    throw new TypeException('Value cannot be cast to float');
            }
        } catch (Exception $exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception $exception) {
            /** @var T */
            return $default;
        }
    }
}
