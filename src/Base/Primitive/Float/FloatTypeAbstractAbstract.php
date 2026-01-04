<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Float;

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
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
abstract readonly class FloatTypeAbstractAbstract extends PrimitiveTypeAbstract implements FloatTypeInterface
{

    /**
     * @throws FloatTypeException
     */
    protected static function getFloatFromString(string $value): float
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

        return (float)$value;
    }

    abstract public static function fromString(string $value): static;

    abstract public static function fromFloat(float $value): static;

    abstract public static function fromInt(int $value): static;

    abstract public static function fromBool(bool $value): static;

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
    ): static|PrimitiveTypeAbstract;

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
    ): static|PrimitiveTypeAbstract;

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
    ): static|PrimitiveTypeAbstract;

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
    ): static|PrimitiveTypeAbstract;

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
    ): static|PrimitiveTypeAbstract;

    abstract public function value(): float;

    public function __toString(): string
    {
        return $this->toString();
    }

    abstract public function toString(): string;

    abstract public function toFloat(): float;

    abstract public function toInt(): int;

    abstract public function toBool(): bool;
}
