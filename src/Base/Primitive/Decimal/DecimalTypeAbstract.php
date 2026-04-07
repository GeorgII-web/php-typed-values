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
abstract readonly class DecimalTypeAbstract extends PrimitiveTypeAbstract implements DecimalTypeInterface
{
    abstract public static function fromBool(bool $value): static;

    abstract public static function fromDecimal(string $value): static;

    abstract public static function fromFloat(float $value): static;

    abstract public static function fromInt(int $value): static;

    abstract public static function fromString(string $value): static;

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
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromDecimal(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static;

    abstract public function value(): string;

    /**
     * Compares a decimal (given as sign + whole + fraction) with an integer bound.
     *
     * @return int -1 if decimal < bound, 0 if equal, 1 if decimal > bound
     */
    private function compareDecimalWithInt(string $sign, string $whole, string $fraction, int $bound): int
    {
        $boundStr = (string) $bound;
        $boundSign = $bound < 0 ? '-' : '';
        $boundAbs = ltrim($boundStr, '-');  // positive absolute value as string

        // Cases with opposite signs
        if ($sign === '-' && $boundSign !== '-') {
            return -1;  // negative < positive
        }
        if ($sign !== '-' && $boundSign === '-') {
            return 1;   // positive > negative
        }

        // Same sign (both non‑negative or both negative)
        if ($sign === '-') {
            // Both negative: compare absolute values (reverse logic)
            $cmpWhole = $this->comparePositiveIntStrings($whole, $boundAbs);
            if ($cmpWhole !== 0) {
                // Larger absolute value => more negative => smaller
                return $cmpWhole > 0 ? -1 : 1;
            }

            // Whole parts equal → fraction decides
            return $this->isFractionZero($fraction) ? 0 : -1;
        }
        // Both non‑negative (including zero)
        $cmpWhole = $this->comparePositiveIntStrings($whole, $boundAbs);
        if ($cmpWhole !== 0) {
            return $cmpWhole;
        }

        // Whole parts equal → fraction > 0 makes decimal larger
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

        if (strlen($a) !== strlen($b)) {
            return strlen($a) <=> strlen($b);
        }

        return $a <=> $b;
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
        $value = trim($value);

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
