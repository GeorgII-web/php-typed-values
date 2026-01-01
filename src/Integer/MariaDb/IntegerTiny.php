<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\MariaDb;

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
 * Database tiny integer (TINYINT signed: -128..127).
 *
 * Represents a tinyint value in the signed range -128..127, matching common
 * MariaDB/MySQL semantics. Factories accept strictly validated strings and
 * native ints and enforce the bounds.
 *
 * Example
 *  - $v = IntegerTiny::fromString('-5');
 *    $v->value(); // -5
 *  - $v = IntegerTiny::fromInt(127);
 *    (string) $v; // "127"
 *
 * @psalm-immutable
 */
class IntegerTiny extends IntType
{
    /** @var int<-128, 127>
     * @readonly */
    protected int $value;

    /**
     * @throws IntegerTypeException
     */
    public function __construct(int $value)
    {
        if ($value < -128 || $value > 127) {
            throw new IntegerTypeException(sprintf('Expected tiny integer in range -128..127, got "%d"', $value));
        }

        $this->value = $value;
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

    /**
     * @return int<-128, 127>
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
                case $value === false:
                    return static::fromInt(0);
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
     * @return int<-128, 127>
     */
    public function toInt(): int
    {
        return $this->value;
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
