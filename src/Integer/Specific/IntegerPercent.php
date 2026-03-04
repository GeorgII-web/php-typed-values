<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\Specific;

use Exception;
use PhpTypedValues\Base\Primitive\Integer\IntegerTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
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
 * IntegerPercent (Percent integer 0-100).
 *
 * Ensures the wrapped value is strictly 0-100. Provides factories
 * from strictly validated string and native int, plus convenient formatting.
 *
 * Example
 *  - $v = IntegerPercent::fromString('1');
 *    $v->value(); // 1 (int)
 *  - $v = IntegerPercent::fromInt(5);
 *    (string) $v; // "5"
 *
 * @psalm-immutable
 */
readonly class IntegerPercent extends IntegerTypeAbstract
{
    /** @var int<0,100> */
    protected int $value;

    /**
     * @throws IntegerTypeException
     */
    public function __construct(int $value)
    {
        if ($value < 0 || $value > 100) {
            throw new IntegerTypeException(sprintf('Expected percent integer, got "%d"', $value));
        }

        $this->value = $value;
    }

    /**
     * @psalm-pure
     *
     * @throws IntegerTypeException
     */
    public static function fromBool(bool $value): static
    {
        return new static(static::boolToInt($value));
    }

    /**
     * @throws DecimalTypeException
     * @throws IntegerTypeException
     *
     * @psalm-pure
     */
    public static function fromDecimal(string $value): static
    {
        return new static(static::decimalToInt($value));
    }

    /**
     * @throws FloatTypeException
     * @throws IntegerTypeException
     *
     * @psalm-pure
     */
    public static function fromFloat(float $value): static
    {
        return new static(static::floatToInt($value));
    }

    /**
     * @psalm-pure
     *
     * @throws IntegerTypeException
     */
    public static function fromInt(int $value): static
    {
        return new static($value);
    }

    /**
     * @throws StringTypeException
     * @throws IntegerTypeException
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
     * @return int<0,100>
     */
    public function jsonSerialize(): int
    {
        return $this->value();
    }

    /**
     * @throws IntegerTypeException
     */
    public function toBool(): bool
    {
        return static::intToBool($this->value());
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
        $toFloatValue = (float) $this->value;

        if ($this->value !== (int) $toFloatValue) {
            throw new IntegerTypeException(sprintf('Integer %s cannot be converted to float without losing precision', $this->value));
        }

        return $toFloatValue;
    }

    /**
     * @return int<0,100>
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
        return static::intToString($this->value());
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
    ): static|PrimitiveTypeAbstract {
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
    ): static|PrimitiveTypeAbstract {
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
    ): static|PrimitiveTypeAbstract {
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
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromInt($value);
        } catch (TypeException) {
            /* @var T $default */
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
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return match (true) {
                is_int($value) => static::fromInt($value),
                is_float($value) => static::fromFloat($value),
                is_bool($value) => static::fromBool($value),
                is_string($value) || $value instanceof Stringable => static::tryFromDecimal((string) $value, static::fromString((string) $value)),
                default => throw new TypeException('Value cannot be cast to int'),
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
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @return int<0,100>
     */
    public function value(): int
    {
        return $this->value;
    }
}
