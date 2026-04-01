<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\MariaDb;

use Exception;
use PhpTypedValues\Base\Primitive\Integer\IntegerTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\Integer\SmallIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function sprintf;

/**
 * IntegerSmall (Database small integer SMALLINT signed: -32768..32767).
 *
 * Represents a smallint value in the signed range -32768..32767, matching common
 * MariaDB/MySQL semantics. Factories accept strictly validated strings and
 * native ints and enforce the bounds.
 *
 * Example
 *  - $v = IntegerSmall::fromString('-5');
 *    $v->value(); // -5
 *  - $v = IntegerSmall::fromInt(32767);
 *    (string) $v; // "32767"
 *
 * @psalm-immutable
 */
class IntegerSmall extends IntegerTypeAbstract
{
    /** @var int<-32768, 32767>
     * @readonly */
    protected int $value;

    /**
     * @throws SmallIntegerTypeException
     */
    public function __construct(int $value)
    {
        if ($value < -32768 || $value > 32767) {
            throw new SmallIntegerTypeException(sprintf('Expected small integer in range -32768..32767, got "%d"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws SmallIntegerTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static(static::boolToInt($value));
    }

    /**
     * @throws DecimalTypeException
     * @throws SmallIntegerTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromDecimal(string $value)
    {
        return new static(static::decimalToInt($value));
    }

    /**
     * @throws FloatTypeException
     * @throws SmallIntegerTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromFloat(float $value)
    {
        return new static(static::floatToInt($value));
    }

    /**
     * @throws SmallIntegerTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static($value);
    }

    /**
     * @throws StringTypeException
     * @throws SmallIntegerTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromString(string $value)
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
     * @return int<-32768, 32767>
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }

    /**
     * @throws IntegerTypeException
     */
    public function toBool(): bool
    {
        return static::intToBool($this->value);
    }

    /**
     * @return non-empty-string
     */
    public function toDecimal(): string
    {
        return static::intToDecimal($this->value);
    }

    /**
     * @throws IntegerTypeException
     */
    public function toFloat(): float
    {
        return static::intToFloat($this->value);
    }

    /**
     * @return int<-32768, 32767>
     */
    public function toInt(): int
    {
        return $this->value;
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return static::intToString($this->value);
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
                    throw new Exception('Value cannot be cast to int');
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

    public function value(): int
    {
        return $this->value;
    }
}
