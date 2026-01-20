<?php

declare(strict_types=1);

namespace PhpTypedValues\Float;

use Exception;
use PhpTypedValues\Base\Primitive\Float\FloatTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Float\FloatTypeException;
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
class FloatNonNegative extends FloatTypeAbstract
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
        if ($value < 0.0) {
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
    public static function fromBool(bool $value)
    {
        return new static(parent::boolToFloat($value));
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
    public static function fromInt(int $value)
    {
        return new static(parent::intToFloat($value));
    }

    /**
     * @throws FloatTypeException
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static(parent::stringToFloat($value));
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isTypeOf(string ...$classNames): bool
    {
        foreach ($classNames as $className) {
            if ($this instanceof $className) {
                return true;
            }
        }

        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    public function jsonSerialize(): float
    {
        return $this->value;
    }

    /**
     * @throws FloatTypeException
     */
    public function toBool(): bool
    {
        return static::floatToBool($this->value);
    }

    public function toFloat(): float
    {
        return $this->value;
    }

    /**
     * @throws FloatTypeException
     */
    public function toInt(): int
    {
        return static::floatToInt($this->value);
    }

    /**
     * @return non-empty-string
     *
     * @throws FloatTypeException
     */
    public function toString(): string
    {
        return static::floatToString($this->value);
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @throws FloatTypeException
     */
    public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        /** @var static */
        return static::fromBool($value);
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = null
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
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromInt($value);
        } catch (Exception $exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     * @param mixed $value
     */
    public static function tryFromMixed(
        $value,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            switch (true) {
                case is_float($value):
                    return static::fromFloat($value);
                case is_int($value):
                    return static::fromInt($value);
                case is_bool($value):
                    return static::fromBool($value);
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
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = null
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

    public function value(): float
    {
        return $this->value;
    }
}
