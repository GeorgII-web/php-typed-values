<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Bool;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_int;
use function is_string;

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
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromInt(
        int $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType {
        try {
            /** @var static */
            return static::fromInt($value);
        } catch (Exception) {
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
    public static function tryFromMixed(
        mixed $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType {
        try {
            /** @var static */
            return match (true) {
                is_bool($value) => static::fromBool($value), // Boolean true\false
                is_int($value) => static::fromInt($value), // Integer 1\0
                ($value === 0.0 || $value === 1.0) => static::fromInt((int) $value), // Floats 1.0\0.0
                //                ($value instanceof self) => static::fromBool($value->value()), // BoolType Class - toString() will care about this case
                is_string($value) || $value instanceof Stringable => static::fromString((string) $value), // String "true","1","yes", etc.
                default => throw new TypeException('Value cannot be cast to boolean'),
            };
        } catch (Exception) {
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
    ): static|PrimitiveType {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
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
