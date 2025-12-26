<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Bool;

use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_scalar;

/**
 * Base implementation for boolean typed values.
 *
 * Provides common formatting helpers and factory methods for bool-backed
 * value objects. Concrete boolean types extend this class and add
 * domain-specific validation if needed.
 *
 * Example
 *  - $v = MyBoolean::fromBool(true);
 *  - $v->toString(); // "true"
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract readonly class BoolType extends PrimitiveType implements BoolTypeInterface
{
    abstract public function value(): bool;

    /**
     * @template T
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromInt(
        int $value,
        mixed $default = new Undefined(),
    ): mixed {
        try {
            /** @var static|T */
            return static::fromInt($value);
        } catch (TypeException) {
            return $default;
        }
    }

    /**
     * @template T
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromMixed(
        mixed $value,
        mixed $default = new Undefined(),
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
     * @template T
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        mixed $default = new Undefined(),
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
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if ($value === null) {
            return '';
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        throw new TypeException('Value cannot be cast to string');
    }

    public function toString(): string
    {
        return $this->value() ? 'true' : 'false';
    }

    public function __toString(): string
    {
        return $this->toString();
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
