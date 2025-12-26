<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Float;

use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_scalar;
use function sprintf;

/**
 * Base implementation for float-typed values.
 *
 * Provides common validation for float strings and formatting helpers for
 * value objects backed by float primitives.
 *
 * Example
 *  - $v = MyFloat::fromString('3.14');
 *  - $v->value(); // 3.14 (float)
 *  - (string) $v; // "3.14"
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract readonly class FloatType extends PrimitiveType implements FloatTypeInterface
{
    abstract public static function fromString(string $value): static;

    abstract public static function fromFloat(float $value): static;

    abstract public function value(): float;

    protected static function assertFloatString(string $value): void
    {
        if (!is_numeric($value)) {
            throw new FloatTypeException(sprintf('String "%s" has no valid float value', $value));
        }

        // Numerical stability check (catches precision loss)
        $floatValue = (float) $value;
        if ($floatValue !== (float) (string) $floatValue) {
            throw new FloatTypeException(sprintf('String "%s" has no valid strict float value', $value));
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
            throw new FloatTypeException(sprintf('String "%s" has invalid formatting (leading zeros or redundant characters)', $value));
        }
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromFloat(
        float $value,
        PrimitiveType $default = new Undefined(),
    ): mixed {
        try {
            /** @var static|T */
            return static::fromFloat($value);
        } catch (TypeException) {
            return $default;
        }
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveType $default = new Undefined(),
    ): mixed {
        try {
            $instance = static::fromString(
                static::convertMixedToString($value)
            );

            /** @var static|T */
            return $instance;
        } catch (TypeException) {
            return $default;
        }
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        PrimitiveType $default = new Undefined(),
    ): mixed {
        try {
            $instance = static::fromString($value);

            /** @var static|T */
            return $instance;
        } catch (TypeException) {
            return $default;
        }
    }

    /**
     * Safely attempts to convert a mixed value to a string.
     * Returns null if conversion is impossible (array, resource, non-stringable object).
     *
     * @throws TypeException
     */
    protected static function convertMixedToString(mixed $value): string
    {
        if (is_scalar($value) || $value === null) {
            return (string) $value;
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        throw new TypeException('Value cannot be cast to string');
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
