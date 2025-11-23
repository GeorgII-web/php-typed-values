<?php

declare(strict_types=1);

namespace PhpTypedValues\Code\Assert;

use PhpTypedValues\Code\Exception\NumericTypeException;
use PhpTypedValues\Code\Exception\StringTypeException;

use function is_numeric;
use function sprintf;

final class Assert
{
    /**
     * @throws NumericTypeException
     */
    public static function greaterThanEq(int|float $value, int|float $min, string $message = ''): void
    {
        if ($value < $min) {
            throw new NumericTypeException($message !== '' ? $message : 'Expected a value greater than or equal to the minimum');
        }
    }

    /**
     * @throws NumericTypeException
     */
    public static function lessThanEq(int|float $value, int|float $max, string $message = ''): void
    {
        if ($value > $max) {
            throw new NumericTypeException($message !== '' ? $message : 'Expected a value less than or equal to the maximum');
        }
    }

    /**
     * Assert that the given value looks like an integer.
     * Accepts numeric strings that represent an integer value (e.g., '5', '-5'),
     * but rejects non-numeric strings and floats with a fractional part (e.g., '5.5', '05').
     *
     * @throws NumericTypeException
     */
    public static function integer(mixed $value, string $message = ''): void
    {
        // Strict check, avoid unexpected string conversion
        $value = (string) $value;
        $convertedValue = (string) ((int) $value);
        if ($value !== $convertedValue) {
            throw new NumericTypeException($message !== '' ? $message : sprintf('Unexpected conversions possible, "%s" !== "%s"', $value, $convertedValue));
        }
    }

    /**
     * Assert that the given value is numeric (int/float or numeric string).
     *
     * @throws NumericTypeException
     */
    public static function numeric(mixed $value, string $message = ''): void
    {
        if (!is_numeric($value)) {
            throw new NumericTypeException($message !== '' ? $message : 'Expected a numeric value');
        }
    }

    /**
     * Assert that the given string is non-empty.
     *
     * @throws StringTypeException
     */
    public static function nonEmptyString(string $value, string $message = ''): void
    {
        if ($value === '') {
            throw new StringTypeException($message !== '' ? $message : 'Value must be a non-empty string');
        }
    }
}
