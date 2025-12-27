<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Float;

use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
use function is_int;
use function is_string;
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
            /** @var static */
            return match (true) {
                is_float($value), is_int($value) => static::fromFloat($value),
                ($value instanceof self) => static::fromFloat($value->value()),
                is_bool($value) => static::fromFloat($value ? 1.0 : 0.0),
                is_string($value) || $value instanceof Stringable => static::fromString((string) $value),
                default => throw new TypeException('Value cannot be cast to float'),
            };
        } catch (TypeException) {
            /** @var T */
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

            /** @var static */
            return $instance;
        } catch (TypeException) {
            /** @var T */
            return $default;
        }
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
