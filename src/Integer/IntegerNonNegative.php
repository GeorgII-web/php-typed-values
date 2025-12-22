<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use PhpTypedValues\Base\Primitive\Integer\IntType;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

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
readonly class IntegerNonNegative extends IntType
{
    /** @var non-negative-int */
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

    public static function tryFromMixed(mixed $value): static|Undefined
    {
        try {
            return static::fromString(
                static::convertMixedToString($value)
            );
        } catch (TypeException) {
            return Undefined::create();
        }
    }

    public static function tryFromString(string $value): static|Undefined
    {
        try {
            return static::fromString($value);
        } catch (TypeException) {
            return Undefined::create();
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
     * @return non-negative-int
     */
    public function value(): int
    {
        return $this->value;
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
