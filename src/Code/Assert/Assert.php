<?php

declare(strict_types=1);

namespace PhpTypedValues\Code\Assert;

use PhpTypedValues\Code\Exception\TypeException;

use function is_numeric;

final class Assert
{
    /**
     * @throws TypeException
     */
    public static function greaterThanEq(int|float $value, int|float $min, string $message = ''): void
    {
        if ($value < $min) {
            throw new TypeException($message !== '' ? $message : 'Expected a value greater than or equal to the minimum');
        }
    }

    /**
     * @throws TypeException
     */
    public static function lessThanEq(int|float $value, int|float $max, string $message = ''): void
    {
        if ($value > $max) {
            throw new TypeException($message !== '' ? $message : 'Expected a value less than or equal to the maximum');
        }
    }

    //    /**
    //     * @throws TypeException
    //     */
    //    public static function greaterThan(int|float $value, int|float $min, string $message = ''): void
    //    {
    //        if ($value <= $min) {
    //            throw new TypeException($message !== '' ? $message : 'Expected a value greater than the minimum');
    //        }
    //    }
    //
    //    /**
    //     * @throws TypeException
    //     */
    //    public static function lessThan(int|float $value, int|float $max, string $message = ''): void
    //    {
    //        if ($value >= $max) {
    //            throw new TypeException($message !== '' ? $message : 'Expected a value less than the maximum');
    //        }
    //    }

    /**
     * Assert that the given value looks like an integer ("integerish").
     * Accepts numeric strings that represent an integer value (e.g., '5', '5.0'),
     * but rejects non-numeric strings and floats with a fractional part (e.g., '5.5').
     *
     * @throws TypeException
     */
    public static function integerish(mixed $value, string $message = ''): void
    {
        if (!is_numeric($value) || $value != (int) $value) {
            throw new TypeException($message !== '' ? $message : 'Expected an "integerish" value');
        }
    }

    /**
     * Assert that the given string is non-empty.
     *
     * @throws TypeException
     */
    public static function nonEmptyString(string $value, string $message = ''): void
    {
        if ($value === '') {
            throw new TypeException($message !== '' ? $message : 'Value must be a non-empty string');
        }
    }
}
