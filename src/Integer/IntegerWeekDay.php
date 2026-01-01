<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use Exception;
use PhpTypedValues\Base\Primitive\Integer\IntType;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_int;
use function is_string;
use function sprintf;

/**
 * Week day number between 1 and 7.
 *
 * Represents an integer constrained to the inclusive range 1..7 where
 * 1 = Monday and 7 = Sunday (or any convention your domain applies).
 * Factories accept strictly validated strings and native ints.
 *
 * Example
 *  - $v = IntegerWeekDay::fromString('5');
 *    $v->value(); // 5
 *  - $v = IntegerWeekDay::fromInt(1);
 *    (string) $v; // "1"
 *
 * @psalm-immutable
 */
class IntegerWeekDay extends IntType
{
    /** @var int<1, 7>
     * @readonly */
    protected int $value;

    /**
     * @throws IntegerTypeException
     */
    public function __construct(int $value)
    {
        if ($value < 1) {
            throw new IntegerTypeException(sprintf('Expected value between 1-7, got "%d"', $value));
        }

        if ($value > 7) {
            throw new IntegerTypeException(sprintf('Expected value between 1-7, got "%d"', $value));
        }

        $this->value = $value;
    }

    /**
     * @return int<1, 7>
     */
    public function toInt(): int
    {
        return $this->value;
    }

    /**
     * @return int<1, 7>
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-return (static&IntType)|T
     */
    public static function tryFromInt(
        int $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static&IntType */
            return static::fromInt($value);
        } catch (TypeException $exception) {
            /* @var T $default */
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
                case is_int($value):
                    return static::fromInt($value);
                case $value === true:
                    return static::fromInt(1);
                case is_string($value) || is_object($value) && method_exists($value, '__toString'):
                    return static::fromString((string) $value);
                default:
                    throw new TypeException('Value cannot be cast to int');
            }
        } catch (Exception $exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @throws IntegerTypeException
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static($value);
    }

    /**
     * @throws IntegerTypeException
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static(parent::getIntegerFromString($value));
    }

    public function jsonSerialize(): int
    {
        return $this->value();
    }

    public function toString(): string
    {
        return (string) $this->value();
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }
}
