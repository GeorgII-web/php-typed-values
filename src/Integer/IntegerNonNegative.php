<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use Exception;
use PhpTypedValues\Base\Primitive\Integer\IntegerTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\Integer\NonNegativeIntegerTypeException;
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
 * IntegerNonNegative (Non-negative integer >= 0).
 *
 * Guarantees the wrapped integer is zero or positive. Provides factories from
 * strictly validated string and native int, along with standard formatting.
 *
 * Example
 *  - $v = IntegerNonNegative::fromString('0');
 *    $v->value(); // 0 (int)
 *  - $v = IntegerNonNegative::fromInt(10);
 *    (string) $v; // "10"
 *
 * @psalm-immutable
 */
class IntegerNonNegative extends IntegerTypeAbstract
{
    /** @var non-negative-int
     * @readonly */
    protected int $value;

    /**
     * @throws NonNegativeIntegerTypeException
     */
    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new NonNegativeIntegerTypeException(sprintf('Expected non-negative integer, got "%d"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws NonNegativeIntegerTypeException
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
     * @throws NonNegativeIntegerTypeException
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
     * @throws NonNegativeIntegerTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromFloat(float $value): self
    {
        return new static(static::floatToInt($value));
    }

    /**
     * @throws NonNegativeIntegerTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromInt(int $value): self
    {
        return new static($value);
    }

    /**
     * @throws NonNegativeIntegerTypeException
     * @return never
     */
    public static function fromNull(null $value)
    {
        throw new NonNegativeIntegerTypeException('Integer type cannot be created from null');
    }

    /**
     * @throws StringTypeException
     * @throws NonNegativeIntegerTypeException
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
     * @return non-negative-int
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

    /**
     * @throws IntegerTypeException
     */
    public function toFloat(): float
    {
        return static::intToFloat($this->value());
    }

    /**
     * @return non-negative-int
     */
    public function toInt(): int
    {
        return $this->value;
    }

    /**
     * @throws NonNegativeIntegerTypeException
     * @return never
     */
    public static function toNull()
    {
        throw new NonNegativeIntegerTypeException('Integer type cannot be converted to null');
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
     * @return non-negative-int
     */
    public function value(): int
    {
        return $this->value;
    }
}
