<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive;

use DateTimeZone;
use Exception;
use PhpTypedValues\Base\Primitive\DateTime\DateTimeTypeInterface;
use PhpTypedValues\Exception\DateTime\ZoneDateTimeTypeException;
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
            throw new ZoneDateTimeTypeException(sprintf('Invalid timezone "%s": %s', $timezone, $e->getMessage()), (int) $e->getCode(), $e);
        }
    }

    /**
     * Returns a normalized string representation of the underlying value.
     */
    abstract public function toString(): string;

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
        if ($intValue !== (int) (float) $intValue) {
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
     */
    protected static function floatToString(float $value): string
    {
        $strValue = (string) $value;
        if ($strValue !== (string) (float) $strValue) {
            throw new FloatTypeException(sprintf('Float "%s" has no valid strict string value', $value));
        }

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
     * @psalm-pure
     *
     * @throws IntegerTypeException
     */
    protected static function intToFloat(int $value): float
    {
        // Numerical stability check (catches precision loss)
        $floatValue = (float) $value;
        if ($floatValue !== (float) (int) $floatValue) {
            throw new IntegerTypeException(sprintf('Integer "%s" has no valid strict float value', $value));
        }

        return $floatValue;
    }

    /**
     * @psalm-pure
     *
     * @return non-empty-string
     *
     * @throws IntegerTypeException
     */
    protected static function intToString(int $value): string
    {
        // Numerical stability check (catches precision loss)
        $stringValue = (string) $value;
        if ($stringValue !== (string) (int) $stringValue) {
            throw new IntegerTypeException(sprintf('Integer "%s" has no valid strict string value', $value));
        }

        return $stringValue;
    }

    /**
     * @psalm-pure
     *
     * @throws IntegerTypeException
     */
    protected static function stringToBool(string $value): bool
    {
        if ($value === 'true') {
            return true;
        }

        if ($value === 'false') {
            return false;
        }

        throw new IntegerTypeException(sprintf('Integer "%s" has no valid strict bool value', $value));
    }

    /**
     * @psalm-pure
     *
     * @throws StringTypeException
     */
    protected static function stringToFloat(string $value): float
    {
        if (!is_numeric($value)) {
            throw new StringTypeException(sprintf('String "%s" has no valid float value', $value));
        }

        // Numerical stability check (catches precision loss)
        $floatValue = (float) $value;
        if ($floatValue !== (float) (string) $floatValue) {
            throw new StringTypeException(sprintf('String "%s" has no valid strict float value', $value));
        }

        // Formatting check: Ensure no leading zeros (unless it's "0" or "0.something")
        // and that the string isn't an integer formatted with a trailing .0 that PHP would drop.
        $normalized = (string) $floatValue;

        // If it's a "clean" float string, PHP's "(string)(float)" cast usually matches
        // the input, UNLESS the input has trailing .0 (like "5.0").
        // If we want to be very strict and reject "0005"
        if (
            $value !== '0'
            && $value !== $normalized
            && $value !== $normalized . '.0'
        ) {
            throw new StringTypeException(sprintf('String "%s" has no valid strict float formatting (leading zeros or redundant characters)', $value));
        }

        return (float) $value;
    }

    /**
     * @psalm-pure
     *
     * @throws StringTypeException
     */
    protected static function stringToInt(string $value): int
    {
        if (!is_numeric($value)) {
            throw new StringTypeException(sprintf('String "%s" has no valid integer value', $value));
        }

        $floatValue = (int) $value;
        if ($floatValue !== (int) (string) $floatValue) {
            throw new StringTypeException(sprintf('String "%s" has no valid strict integer value', $value));
        }

        return $floatValue;
    }

    /**
     * Alias of {@see toString} for convenient casting.
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
