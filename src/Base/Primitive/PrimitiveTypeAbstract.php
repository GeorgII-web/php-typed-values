<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive;

use DateTimeZone;
use Exception;
use PhpTypedValues\Base\Primitive\DateTime\DateTimeTypeInterface;
use PhpTypedValues\Exception\DateTime\ZoneDateTimeTypeException;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;

use function sprintf;

/**
 * Base class for immutable typed values.
 *
 * Responsibilities
 *  - Define a common root for value objects in this library.
 *  - Enforce immutability and a unified interface via {@see PrimitiveTypeInterface}.
 *  - Mark all descendants as implementing {@see JsonSerializable} — concrete
 *    classes SHOULD implement `jsonSerialize()` consistently with their
 *    `toString()`/`value()` representation.
 *
 * Notes
 *  - This class does not provide storage or behavior by itself; concrete
 *    subclasses define validation, factories (e.g. `fromString()`), and accessors
 *    (e.g. `value()` and formatting helpers).
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract readonly class PrimitiveTypeAbstract implements PrimitiveTypeInterface
{
    /**
     * Returns true if the Object value is empty.
     */
    abstract public function isEmpty(): bool;

    /**
     * Checks if the current object (or its parents) is an instance of the provided class names.
     */
    abstract public function isTypeOf(string ...$classNames): bool;

    /**
     * Returns if the Object value is an Undefined type class.
     */
    abstract public function isUndefined(): bool;

    /**
     * JSON representation of the value.
     *
     * Marked as mutation-free so Psalm treats calls as pure in immutable contexts.
     *
     * @psalm-pure
     */
    abstract public function jsonSerialize(): mixed;

    /**
     * @psalm-pure
     *
     * @param non-empty-string $timezone
     *
     * @throws ZoneDateTimeTypeException
     */
    public static function stringToDateTimeZone(string $timezone = DateTimeTypeInterface::DEFAULT_ZONE): DateTimeZone
    {
        try {
            return new DateTimeZone($timezone);
        } catch (Exception $e) {
            throw new ZoneDateTimeTypeException(sprintf('Invalid timezone "%s": %s', $timezone, $e->getMessage()), 0, $e);
        }
    }

    /**
     * Returns a normalized string representation of the underlying value.
     */
    abstract public function toString(): string;

    /**
     * @psalm-pure
     *
     * @return non-empty-string
     */
    protected static function boolToDecimal(bool $value): string
    {
        if ($value === true) {
            return '1.0';
        }

        return '0.0';
    }

    /**
     * @psalm-pure
     */
    protected static function boolToFloat(bool $value): float
    {
        if ($value === true) {
            return 1.0;
        }

        return 0.0;
    }

    /**
     * @psalm-pure
     *
     * @return non-negative-int 1|0
     */
    protected static function boolToInt(bool $value): int
    {
        if ($value === true) {
            return 1;
        }

        return 0;
    }

    /**
     * @psalm-pure
     *
     * @return non-empty-string
     */
    protected static function boolToString(bool $value): string
    {
        if ($value === true) {
            return 'true';
        }

        return 'false';
    }

    /**
     * @psalm-pure
     *
     * @throws DecimalTypeException
     */
    protected static function decimalToBool(string $value): bool
    {
        if ($value === '1.0') {
            return true;
        }

        if ($value === '0.0') {
            return false;
        }

        throw new DecimalTypeException(sprintf('Decimal "%s" has no valid strict bool value', $value));
    }

    /**
     * @psalm-pure
     *
     * @throws DecimalTypeException
     */
    protected static function decimalToInt(string $value): int
    {
        $intValue = (int) $value;
        if ($value !== self::intToDecimal($intValue)) {
            throw new DecimalTypeException(sprintf('Decimal "%s" has no valid strict int value', $value));
        }

        return $intValue;
    }

    /**
     * @psalm-pure
     */
    protected static function decimalToString(string $value): string
    {
        return $value;
    }

    /**
     * @psalm-pure
     *
     * @throws FloatTypeException
     */
    protected static function floatToBool(float $value): bool
    {
        if ($value === 1.0) {
            return true;
        }

        if ($value === 0.0) {
            return false;
        }

        throw new FloatTypeException(sprintf('Float "%s" has no valid strict bool value', $value));
    }

    /**
     * @psalm-pure
     *
     * @throws FloatTypeException
     */
    protected static function floatToInt(float $value): int
    {
        $intValue = (int) $value;
        if ($value !== (float) $intValue) {
            throw new FloatTypeException(sprintf('Float "%s" has no valid strict int value', $value));
        }

        return $intValue;
    }

    /**
     * @psalm-pure
     *
     * @return non-empty-string
     *
     * @throws FloatTypeException
     * @throws StringTypeException
     */
    protected static function floatToString(float $value, bool $roundTripConversion = true): string
    {
        // Convert to string as it stored in memory
        $strValue = sprintf('%.17f', $value);

        // Trim trailing zeros but keep at least one decimal
        $strValue = rtrim($strValue, '0');
        if (str_ends_with($strValue, '.')) {
            $strValue .= '0';
        }

        if ($roundTripConversion && $value !== self::stringToFloat($strValue, false)) {
            throw new FloatTypeException(sprintf('Float "%s" has no valid strict string value', $value));
        }

        /**
         * @var non-empty-string
         */
        return $strValue;
    }

    /**
     * @psalm-pure
     *
     * @throws IntegerTypeException
     */
    protected static function intToBool(int $value): bool
    {
        if ($value === 1) {
            return true;
        }

        if ($value === 0) {
            return false;
        }

        throw new IntegerTypeException(sprintf('Integer "%s" has no valid strict bool value', $value));
    }

    /**
     * Safe cast to decimal, no edge cases exist.
     *
     * @psalm-pure
     *
     * @return non-empty-string
     */
    protected static function intToDecimal(int $value): string
    {
        return $value . '.0';
    }

    /**
     * @psalm-pure
     *
     * @throws IntegerTypeException
     */
    protected static function intToFloat(int $value): float
    {
        $floatValue = (float) $value;
        if ($value !== (int) $floatValue) {
            throw new IntegerTypeException(sprintf('Integer "%s" has no valid strict float value', $value));
        }

        return $floatValue;
    }

    /**
     * Safe cast to string, no edge cases exist.
     *
     * @psalm-pure
     *
     * @return non-empty-string
     */
    protected static function intToString(int $value): string
    {
        return (string) $value;
    }

    /**
     * @psalm-pure
     *
     * @throws StringTypeException
     */
    protected static function stringToBool(string $value): bool
    {
        if ($value === 'true') {
            return true;
        }

        if ($value === 'false') {
            return false;
        }

        throw new StringTypeException(sprintf('String "%s" has no valid strict bool value', $value));
    }

    /**
     * @psalm-pure
     *
     * @return non-empty-string
     *
     * @throws DecimalTypeException
     */
    protected static function stringToDecimal(string $value): string
    {
        if (trim($value) === '') {
            throw new DecimalTypeException(sprintf('String "%s" has no valid decimal value', $value));
        }

        $isDecimal = preg_match('/^-?(?:0|[1-9]\d*)\.\d+$/', $value) === 1;
        $isInteger = preg_match('/^-?[1-9]\d+$/', $value) === 1;

        if (!$isDecimal && !$isInteger) {
            throw new DecimalTypeException(sprintf('String "%s" has no valid strict decimal value', $value));
        }

        if ($isDecimal && preg_match('/^-?(?:0|[1-9]\d*)\.(?:0|.*[1-9])$/', $value) !== 1) {
            throw new DecimalTypeException(sprintf('String "%s" has no valid strict decimal value', $value));
        }

        /**
         * @var non-empty-string $value
         */
        return $value;
    }

    /**
     * @psalm-pure
     *
     * @throws StringTypeException
     * @throws FloatTypeException
     */
    protected static function stringToFloat(string $value, bool $roundTripConversion = true): float
    {
        if (!is_numeric($value)) {
            throw new StringTypeException(sprintf('String "%s" has no valid float value', $value));
        }

        $floatValue = (float) $value;

        // Formatting check: Ensure no leading zeros (unless it's "0" or "0.something")
        // and that the string isn't an integer formatted with a trailing .0 that PHP would drop.
        $normalized = self::floatToString($floatValue, false);

        // Numerical stability check (catches precision loss)
        if ($roundTripConversion && $normalized !== $value) {
            throw new StringTypeException(sprintf('String "%s" has no valid strict float value', $value));
        }

        return $floatValue;
    }

    /**
     * @psalm-pure
     *
     * @throws StringTypeException
     */
    protected static function stringToInt(string $value): int
    {
        $intValue = (int) $value;
        if ($value !== (string) $intValue) {
            throw new StringTypeException(sprintf('String "%s" has no valid strict integer value', $value));
        }

        return $intValue;
    }

    /**
     * Alias of {@see toString} for convenient casting.
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
