<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use Exception;
use PhpTypedValues\Base\Primitive\Integer\IntType;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Exception\ReasonableRangeIntegerTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
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
readonly class IntegerWeekDay extends IntType
{
    /** @var int<1, 7> */
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
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType {
        try {
            /** @var static&IntType */
            return static::fromInt($value);
        } catch (TypeException) {
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
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
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
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType {
        try {
            /** @var static */
            return match (true) {
                is_int($value) => static::fromInt($value),
                is_float($value) => static::fromFloat($value),
                is_bool($value) => static::fromBool($value),
                is_string($value) || $value instanceof Stringable => static::fromString((string) $value),
                default => throw new TypeException('Value cannot be cast to int'),
            };
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @throws IntegerTypeException
     */
    public static function fromInt(int $value): static
    {
        return new static($value);
    }

    /**
     * @throws IntegerTypeException
     * @throws ReasonableRangeIntegerTypeException
     */
    public static function fromFloat(float $value): static
    {
        return new static(parent::getIntegerFromFloat($value));
    }

    /**
     * @throws IntegerTypeException
     */
    public static function fromBool(bool $value): static
    {
        return new static((int) $value);
    }

    /**
     * @throws IntegerTypeException
     */
    public static function fromString(string $value): static
    {
        return new static(parent::getIntegerFromString($value));
    }

    /**
     * @return int<1, 7>
     */
    public function jsonSerialize(): int
    {
        return $this->value();
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return (string) $this->value();
    }

    public function toFloat(): float
    {
        return $this->value();
    }

    public function toBool(): bool
    {
        return (bool) $this->value();
    }

    public function isEmpty(): false
    {
        return false;
    }

    public function isUndefined(): false
    {
        return false;
    }
}
