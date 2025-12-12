<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use PhpTypedValues\Abstract\Integer\IntType;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

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
    public function value(): int
    {
        return $this->value;
    }

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value)
    {
        try {
            return static::fromString($value);
        } catch (TypeException $exception) {
            return Undefined::create();
        }
    }

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromInt(int $value)
    {
        try {
            return static::fromInt($value);
        } catch (TypeException $exception) {
            return Undefined::create();
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
        parent::assertIntegerString($value);

        return new static((int) $value);
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
}
