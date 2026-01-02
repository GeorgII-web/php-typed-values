<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\MariaDb;

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
readonly class IntegerTiny extends IntType
{
    /** @var int<-128, 127> */
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
     */
    public static function fromInt(int $value): static
    {
        return new static($value);
    }

    /**
     * @throws IntegerTypeException
     */
    public static function fromString(string $value): static
    {
        return new static(parent::getIntegerFromString($value));
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
     * @return int<-128, 127>
     */
    public function toInt(): int
    {
        return $this->value;
    }

    /**
     * @return int<-128, 127>
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
