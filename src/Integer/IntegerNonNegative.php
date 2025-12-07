<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use PhpTypedValues\Abstract\Integer\IntType;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

use function sprintf;

/**
 * Non-negative integer (>= 0).
 *
 * Example "0"
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

    /**
     * @return non-negative-int
     */
    public function value(): int
    {
        return $this->value;
    }
}
