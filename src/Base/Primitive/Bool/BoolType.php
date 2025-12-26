<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Bool;

use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

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
}
