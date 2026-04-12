<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\Specific;

use Exception;
use PhpTypedValues\Base\Primitive\Integer\IntegerTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\HourIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function sprintf;

/**
 * IntegerHour (Hour number between 0 and 23).
 *
 * Represents an integer constrained to the inclusive range 0..23.
 * Factories accept strictly validated strings and native ints.
 *
 * Example
 *  - $v = IntegerHour::fromString('14');
 *    $v->value(); // 14
 *  - $v = IntegerHour::fromInt(9);
 *    (string) $v; // "9"
 *
 * @psalm-immutable
 */
class IntegerHour extends IntegerTypeAbstract
{
    /** @var int<0, 23>
     * @readonly */
    protected int $value;

    /**
     * @throws HourIntegerTypeException
     */
    public function __construct(int $value)
    {
        if ($value < 0 || $value > 23) {
            throw new HourIntegerTypeException(sprintf('Expected value between 0-23, got "%d"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws HourIntegerTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromBool(bool $value): self
    {
        return new static(static::boolToInt($value));
    }

    /**
     * @throws DecimalTypeException
     * @throws HourIntegerTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromDecimal(string $value): self
    {
        return new static(static::decimalToInt($value));
    }

    /**
     * @throws FloatTypeException
     * @throws HourIntegerTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromFloat(float $value): self
    {
        return new static(static::floatToInt($value));
    }

    /**
     * @throws HourIntegerTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromInt(int $value): self
    {
        return new static($value);
    }

    /**
     * @throws HourIntegerTypeException
     * @return never
     */
    public static function fromNull(null $value)
    {
        throw new HourIntegerTypeException('Integer type cannot be created from null');
    }

    /**
     * @throws StringTypeException
     * @throws HourIntegerTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromString(string $value): self
    {
        return new static(static::stringToInt($value));
    }

    /**
     * @return false
     */
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

    /**
     * @return false
     */
    public function isUndefined(): bool
    {
        return false;
    }

    /**
     * @return int<0, 23>
     */
    public function jsonSerialize(): int
    {
        return $this->value();
    }

    public function toBool(): bool
    {
        return (bool) $this->value();
    }

    public function toDecimal(): string
    {
        return static::intToDecimal($this->value());
    }

    public function toFloat(): float
    {
        return $this->value();
    }

    /**
     * @return int<0, 23>
     */
    public function toInt(): int
    {
        return $this->value;
    }

    /**
     * @throws HourIntegerTypeException
     * @return never
     */
    public static function toNull()
    {
        throw new HourIntegerTypeException('Integer type cannot be converted to null');
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return (string) $this->value();
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromBool($value);
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
     *
     * @psalm-pure
     */
    public static function tryFromDecimal(
        string $value,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromDecimal($value);
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
     *
     * @psalm-pure
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
     *
     * @psalm-pure
     */
    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromInt($value);
        } catch (TypeException $exception) {
            // @var T
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     * @param mixed $value
     */
    public static function tryFromMixed(
        $value,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            switch (true) {
                case is_int($value):
                    return static::fromInt($value);
                case is_float($value):
                    return static::fromFloat($value);
                case is_bool($value):
                    return static::fromBool($value);
                case is_string($value) || is_object($value) && method_exists($value, '__toString'):
                    return static::tryFromDecimal((string) $value, static::fromString((string) $value));
                default:
                    throw new TypeException('Value cannot be cast to int');
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
     *
     * @psalm-pure
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

    /**
     * @return int<0, 23>
     */
    public function value(): int
    {
        return $this->value;
    }
}
