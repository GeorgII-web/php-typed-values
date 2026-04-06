<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\MariaDb;

use Exception;
use PhpTypedValues\Base\Primitive\Integer\IntegerTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\Integer\UnsignedSmallIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function sprintf;

/**
 * IntegerSmallUnsigned (Database small integer SMALLINT unsigned: 0..65535).
 *
 * Represents an unsigned smallint value in the range 0..65535, matching common
 * MariaDB/MySQL semantics. Factories accept strictly validated strings and
 * native ints and enforce the bounds.
 *
 * Example
 *  - $v = IntegerSmallUnsigned::fromString('65535');
 *    $v->value(); // 65535
 *  - $v = IntegerSmallUnsigned::fromInt(0);
 *    (string) $v; // "0"
 *
 * @psalm-immutable
 */
readonly class IntegerSmallUnsigned extends IntegerTypeAbstract
{
    /** @var int<0, 65535> */
    protected int $value;

    /**
     * @throws UnsignedSmallIntegerTypeException
     */
    public function __construct(int $value)
    {
        if ($value < 0 || $value > 65535) {
            throw new UnsignedSmallIntegerTypeException(sprintf('Expected unsigned small integer in range 0..65535, got "%d"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws UnsignedSmallIntegerTypeException
     *
     * @psalm-pure
     */
    public static function fromBool(bool $value): static
    {
        return new static(static::boolToInt($value));
    }

    /**
     * @throws DecimalTypeException
     * @throws UnsignedSmallIntegerTypeException
     *
     * @psalm-pure
     */
    public static function fromDecimal(string $value): static
    {
        return new static(static::decimalToInt($value));
    }

    /**
     * @throws FloatTypeException
     * @throws UnsignedSmallIntegerTypeException
     *
     * @psalm-pure
     */
    public static function fromFloat(float $value): static
    {
        return new static(static::floatToInt($value));
    }

    /**
     * @throws UnsignedSmallIntegerTypeException
     *
     * @psalm-pure
     */
    public static function fromInt(int $value): static
    {
        return new static($value);
    }

    /**
     * @throws StringTypeException
     * @throws UnsignedSmallIntegerTypeException
     *
     * @psalm-pure
     */
    public static function fromString(string $value): static
    {
        return new static(static::stringToInt($value));
    }

    public function isEmpty(): false
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

    public function isUndefined(): false
    {
        return false;
    }

    /**
     * @return int<0, 65535>
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
     * @return int<0, 65535>
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
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromBool($value);
        } catch (Exception) {
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
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromDecimal($value);
        } catch (Exception) {
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
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromFloat($value);
        } catch (Exception) {
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
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromInt($value);
        } catch (Exception) {
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
    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return match (true) {
                is_int($value) => static::fromInt($value),
                is_float($value) => static::fromFloat($value),
                is_bool($value) => static::fromBool($value),
                is_string($value) || $value instanceof Stringable => static::tryFromDecimal((string) $value, static::fromString((string) $value)),
                default => throw new Exception('Value cannot be cast to int'),
            };
        } catch (Exception) {
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
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    public function value(): int
    {
        return $this->value;
    }
}
