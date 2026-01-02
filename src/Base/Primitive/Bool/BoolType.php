<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Bool;

use PhpTypedValues\Base\Primitive\PrimitiveType;
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
abstract class BoolType extends PrimitiveType implements BoolTypeInterface
{
    /**
     * DO NOT IMPLEMENT ANY PUBLIC METHODS IN INTERNAL CLASS!
     */
    abstract public function value(): bool;

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromInt(
        int $value,
        PrimitiveType $default = null
    );

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     * @param mixed $value
     */
    abstract public static function tryFromMixed(
        $value,
        PrimitiveType $default = null
    );

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromString(
        string $value,
        PrimitiveType $default = null
    );

    abstract public function toString(): string;

    public function __toString(): string
    {
        return $this->toString();
    }

    abstract public function isEmpty(): bool;

    abstract public function isUndefined(): bool;
}
