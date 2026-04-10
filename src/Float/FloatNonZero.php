<?php

declare(strict_types=1);

namespace PhpTypedValues\Float;

use Exception;
use PhpTypedValues\Base\Primitive\Float\FloatTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Float\NonZeroFloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
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
 * Non-zero float-typed value (!= 0.0).
 *
 * @psalm-immutable
 */
readonly class FloatNonZero extends FloatTypeAbstract
{
    protected float $value;

    /**
     * @throws NonZeroFloatTypeException
     */
    public function __construct(float $value)
    {
        if ($value === 0.0) {
            throw new NonZeroFloatTypeException(sprintf('Expected non-zero float, got "%s"', $value));
        }

        if (is_infinite($value)) {
            throw new NonZeroFloatTypeException('Infinite float value');
        }

        if (is_nan($value)) {
            throw new NonZeroFloatTypeException('Not a number float value');
        }

        $this->value = $value;
    }

    /**
     * @throws NonZeroFloatTypeException
     *
     * @psalm-pure
     */
    public static function fromBool(bool $value): static
    {
        return new static(parent::boolToFloat($value));
    }

    /**
     * @psalm-pure
     *
     * @throws DecimalTypeException
     * @throws NonZeroFloatTypeException
     * @throws StringTypeException
     * @throws FloatTypeException
     */
    public static function fromDecimal(string $value): static
    {
        return new static(static::decimalToFloat($value));
    }

    /**
     * @throws NonZeroFloatTypeException
     *
     * @psalm-pure
     */
    public static function fromFloat(float $value): static
    {
        return new static($value);
    }

    /**
     * @throws NonZeroFloatTypeException
     * @throws IntegerTypeException
     *
     * @psalm-pure
     */
    public static function fromInt(int $value): static
    {
        return new static(parent::intToFloat($value));
    }

    /**
     * @throws NonZeroFloatTypeException
     */
    public static function fromNull(null $value): never
    {
        throw new NonZeroFloatTypeException('Float type cannot be created from null');
    }

    /**
     * @throws NonZeroFloatTypeException
     * @throws StringTypeException
     * @throws FloatTypeException
     *
     * @psalm-pure
     */
    public static function fromString(string $value): static
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

    /**
     * @throws FloatTypeException
     * @throws StringTypeException
     * @throws DecimalTypeException
     */
    public function toDecimal(): string
    {
        return static::floatToDecimal($this->value());
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
     * @throws NonZeroFloatTypeException
     */
    public static function toNull(): never
    {
        throw new NonZeroFloatTypeException('Float type cannot be converted to null');
    }

    /**
     * @return non-empty-string
     *
     * @throws FloatTypeException
     * @throws StringTypeException
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
                is_float($value) => static::fromFloat($value),
                is_int($value) => static::fromInt($value),
                is_bool($value) => static::fromBool($value),
                is_string($value) || $value instanceof Stringable => static::fromString((string) $value),
                default => throw new TypeException('Value cannot be cast to float'),
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

    public function value(): float
    {
        return $this->value;
    }
}
