<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Decimal;

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

use function strlen;

/**
 * Base implementation for Decimal-typed values.
 *
 * Provides common formatting helpers for value objects backed by Decimals.
 * Concrete Decimal types extend this class and add domain-specific
 * validation/normalization.
 *
 * Example
 *  - $v = Decimal::fromString('hello');
 *  - $v->toString(); // "hello"
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract class DecimalTypeAbstract extends PrimitiveTypeAbstract implements DecimalTypeInterface
{
    /**
     * @return static
     */
    abstract public static function fromBool(bool $value);

    /**
     * @return static
     */
    abstract public static function fromDecimal(string $value);

    /**
     * @return static
     */
    abstract public static function fromFloat(float $value);

    /**
     * @return static
     */
    abstract public static function fromInt(int $value);

    /**
     * @return never
     * @param null $value
     */
    abstract public static function fromNull($value);

    /**
     * @return static
     */
    abstract public static function fromString(string $value);

    abstract public function isTypeOf(string ...$classNames): bool;

    /**
     * Checks if a decimal string (e.g., "1.000000000000000000000000000000000000123")
     * lies within the inclusive integer range [$from, $to].
     *
     * @throws DecimalTypeException
     */
    public function isValidRange(string $value, int $from, int $to): bool
    {
        // 1. Parse the decimal string
        $parsed = $this->parseDecimalString($value);
        $sign = $parsed['sign'];
        $whole = $parsed['whole'];
        $fraction = $parsed['fraction'];

        // 2. Compare with lower bound ($from)
        if ($this->compareDecimalWithInt($sign, $whole, $fraction, $from) < 0) {
            return false;
        }

        // 3. Compare with upper bound ($to)
        if ($this->compareDecimalWithInt($sign, $whole, $fraction, $to) > 0) {
            return false;
        }

        return true;
    }

    abstract public function toBool(): bool;

    abstract public function toDecimal(): string;

    abstract public function toFloat(): float;

    abstract public function toInt(): int;

    /**
     * @return never
     */
    abstract public function toNull();

    abstract public function toString(): string;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromDecimal(
        string $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     * @param mixed $value
     */
    abstract public static function tryFromMixed(
        $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = null
    );

    abstract public function value(): string;

    /**
     * Compares a decimal (given as sign + whole + fraction) with an integer bound.
     *
     * @return int -1 if decimal < bound, 0 if equal, 1 if decimal > bound
     */
    private function compareDecimalWithInt(string $sign, string $whole, string $fraction, int $bound): int
    {
        $boundStr = (string) $bound;
        $isBoundNegative = $bound < 0;
        $boundAbs = ltrim($boundStr, '-');

        // Cases with opposite signs
        if ($sign === '-' && !$isBoundNegative) {
            return -1;
        }
        if ($sign !== '-' && $isBoundNegative) {
            return 1;
        }

        // Same sign (both negative or both non-negative)
        $cmpWhole = $this->comparePositiveIntStrings($whole, $boundAbs);
        if ($sign === '-') {
            // Both negative: compare absolute values (reverse logic)
            if ($cmpWhole > 0) {
                return -1;
            }
            if ($cmpWhole < 0) {
                return 1;
            }

            return $this->isFractionZero($fraction) ? 0 : -1;
        }

        // Both non-negative (including zero)
        if ($cmpWhole !== 0) {
            return $cmpWhole;
        }

        return $this->isFractionZero($fraction) ? 0 : 1;
    }

    /**
     * Compares two positive integer strings (may have leading zeros).
     * Returns -1, 0, or 1.
     */
    private function comparePositiveIntStrings(string $a, string $b): int
    {
        $a = ltrim($a, '0');
        $b = ltrim($b, '0');
        $a = $a === '' ? '0' : $a;
        $b = $b === '' ? '0' : $b;

        $lenA = strlen($a);
        $lenB = strlen($b);

        return ($lenA <=> $lenB) ?: ($a <=> $b);
    }

    /**
     * Checks if a fraction string consists only of zeros.
     */
    private function isFractionZero(string $fraction): bool
    {
        return preg_match('/^0*$/', $fraction) === 1;
    }

    /**
     * Parses a decimal string into sign, whole part, and fraction part.
     *
     * @throws DecimalTypeException
     */
    private function parseDecimalString(string $value): array
    {
        // Regex: optional sign, then either digits + optional decimal, or .digits
        // It captures: [1] sign, [2] whole part, [3] fraction part
        if (
            !preg_match('/^([+-]?)(\d*)(?:\.(\d*))?$/', $value, $matches)
            || ($matches[2] === '' && ($matches[3] ?? '') === '')
        ) {
            throw new DecimalTypeException('String "' . $value . '" has no valid decimal value');
        }

        $sign = $matches[1];          // '' or '-'
        $whole = $matches[2] === '' ? '0' : $matches[2];
        $fraction = $matches[3] ?? '';
        if ($fraction === '') {
            $fraction = '0';
        }

        return [
            'sign' => $sign,
            'whole' => $whole,
            'fraction' => $fraction,
        ];
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
