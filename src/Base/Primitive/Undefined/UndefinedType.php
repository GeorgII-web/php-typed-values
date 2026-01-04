<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Undefined;

use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Base implementation for a special "Undefined/Unknown" typed value.
 *
 * Represents an explicit absence of a meaningful value.
 * Methods that would normally expose a value or its string representation
 * intentionally throw to prevent accidental usage.
 *
 * Example
 *  - $u = Undefined::create();
 *  - $u->value(); // throws UndefinedTypeException
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract class UndefinedType extends PrimitiveType implements UndefinedTypeInterface
{
    /**
     * @return static
     */
    abstract public static function create();

    /**
     * @return static
     */
    abstract public static function tryFromBool(
        bool $value,
        PrimitiveType $default = null
    );

    /**
     * @return static
     */
    abstract public static function tryFromFloat(
        float $value,
        PrimitiveType $default = null
    );

    /**
     * @return static
     */
    abstract public static function tryFromInt(
        int $value,
        PrimitiveType $default = null
    );

    /**
     * @return static
     */
    abstract public static function tryFromString(
        string $value,
        PrimitiveType $default = null
    );

    /**
     * @return static
     */
    abstract public static function tryFromArray(
        array $value,
        PrimitiveType $default = null
    );

    /**
     * @param mixed $value
     * @return static
     */
    abstract public static function tryFromMixed(
        $value,
        PrimitiveType $default = null
    );

    /**
     * @return static
     */
    abstract public static function fromBool(bool $value);

    /**
     * @return static
     */
    abstract public static function fromFloat(float $value);

    /**
     * @return static
     */
    abstract public static function fromInt(int $value);

    /**
     * @return static
     */
    abstract public static function fromString(string $value);

    /**
     * @return static
     */
    abstract public static function fromArray(array $value);

    /**
     * @throws UndefinedTypeException
     * @return never
     */
    abstract public function toInt();

    /**
     * @throws UndefinedTypeException
     * @return never
     */
    abstract public function toFloat();

    /**
     * @throws UndefinedTypeException
     * @return never
     */
    abstract public function toBool();

    /**
     * @throws UndefinedTypeException
     */
    abstract public function toString(): string;

    /**
     * @throws UndefinedTypeException
     * @return never
     */
    abstract public function toArray();

    /**
     * @throws UndefinedTypeException
     */
    abstract public function value(): string;
}
