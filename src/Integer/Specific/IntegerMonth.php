<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\Specific;

use Exception;
use PhpTypedValues\Base\Primitive\Integer\IntegerTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\MonthIntegerTypeException;
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
 * IntegerMonth (Month number between 1 and 12).
 *
 * Represents an integer constrained to the inclusive range 1..12 where
 * 1 = January and 12 = December.
 * Factories accept strictly validated strings and native ints.
 *
 * Example
 *  - $v = IntegerMonth::fromString('5');
 *    $v->value(); // 5
 *  - $v = IntegerMonth::fromInt(1);
 *    (string) $v; // "1"
 *
 * @psalm-immutable
 */
class IntegerMonth extends IntegerTypeAbstract
{
    /** @var int<1, 12>
     * @readonly */
    protected int $value;

    /**
     * @throws MonthIntegerTypeException
     */
    public function __construct(int $value)
    {
        if ($value < 1) {
            throw new MonthIntegerTypeException(sprintf('Expected value between 1-12, got "%d"', $value));
        }

        if ($value > 12) {
            throw new MonthIntegerTypeException(sprintf('Expected value between 1-12, got "%d"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws MonthIntegerTypeException
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
     * @throws MonthIntegerTypeException
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
     * @throws MonthIntegerTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromFloat(float $value)
    {
        return new static(static::floatToInt($value));
    }

    /**
     * @throws MonthIntegerTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static($value);
    }

    /**
     * @throws MonthIntegerTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromLabel(string $label)
    {
        switch ($label) {
            case 'January':
                $value = 1;
                break;
            case 'February':
                $value = 2;
                break;
            case 'March':
                $value = 3;
                break;
            case 'April':
                $value = 4;
                break;
            case 'May':
                $value = 5;
                break;
            case 'June':
                $value = 6;
                break;
            case 'July':
                $value = 7;
                break;
            case 'August':
                $value = 8;
                break;
            case 'September':
                $value = 9;
                break;
            case 'October':
                $value = 10;
                break;
            case 'November':
                $value = 11;
                break;
            case 'December':
                $value = 12;
                break;
            default:
                throw new MonthIntegerTypeException(sprintf('Expected month label, got "%s"', $label));
        }

        return new static($value);
    }

    /**
     * @throws StringTypeException
     * @throws MonthIntegerTypeException
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
     * @return int<1, 12>
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
     * @return int<1, 12>
     */
    public function toInt(): int
    {
        return $this->value;
    }

    /**
     * @return non-empty-string
     */
    public function toLabel(): string
    {
        switch ($this->value) {
            case 1:
                return 'January';
            case 2:
                return 'February';
            case 3:
                return 'March';
            case 4:
                return 'April';
            case 5:
                return 'May';
            case 6:
                return 'June';
            case 7:
                return 'July';
            case 8:
                return 'August';
            case 9:
                return 'September';
            case 10:
                return 'October';
            case 11:
                return 'November';
            case 12:
                return 'December';
        }
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
     * @return int<1, 12>
     */
    public function value(): int
    {
        return $this->value;
    }
}
