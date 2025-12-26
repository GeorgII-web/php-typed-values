<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use PhpTypedValues\Base\Primitive\Integer\IntType;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

use function sprintf;

/**
 * Positive integer (> 0).
 *
 * Ensures the wrapped value is strictly greater than zero. Provides factories
 * from strictly validated string and native int, plus convenient formatting.
 *
 * Example
 *  - $v = IntegerPositive::fromString('1');
 *    $v->value(); // 1 (int)
 *  - $v = IntegerPositive::fromInt(5);
 *    (string) $v; // "5"
 *
 * @psalm-immutable
 */
readonly class IntegerPositive extends IntType
{
    /** @var positive-int */
    protected int $value;

    /**
     * @throws IntegerTypeException
     */
    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new IntegerTypeException(sprintf('Expected positive integer, got "%d"', $value));
        }

        $this->value = $value;
    }

    /**
     * @template T
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromMixed(mixed $value, mixed $default = new Undefined()): mixed
    {
        try {
            return static::fromString(
                static::convertMixedToString($value)
            );
        } catch (TypeException) {
            return $default;
        }
    }

    /**
     * @template T
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(string $value, mixed $default = new Undefined()): mixed
    {
        try {
            return static::fromString($value);
        } catch (TypeException) {
            return $default;
        }
    }

    public static function tryFromInt(int $value): static|Undefined
    {
        try {
            return static::fromInt($value);
        } catch (TypeException) {
            return Undefined::create();
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
     */
    public static function fromString(string $value): static
    {
        parent::assertIntegerString($value);

        return new static((int) $value);
    }

    /**
     * @return positive-int
     */
    public function toInt(): int
    {
        return $this->value;
    }

    /**
     * @return positive-int
     */
    public function value(): int
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

    public function __toString(): string
    {
        return $this->toString();
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
