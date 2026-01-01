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
 * Non‑negative integer (>= 0).
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
class IntegerNonNegative extends IntType
{
    /** @var non-negative-int
     * @readonly */
    protected int $value;

    /**
     * @throws IntegerTypeException
     */
    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new IntegerTypeException(sprintf('Expected non-negative integer, got "%d"', $value));
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
     * @return non-negative-int
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
     * @return non-negative-int
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
