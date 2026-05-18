<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Undefined;

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
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
abstract class UndefinedTypeAbstract extends PrimitiveTypeAbstract implements UndefinedTypeInterface
{
    /**
     * @return static
     */
    abstract public static function create();

    /**
     * @throws UndefinedTypeException
     * @return static
     */
    abstract public static function fromBool(bool $value);

    /**
     * @throws UndefinedTypeException
     * @return static
     */
    abstract public static function fromDecimal(string $value);

    /**
     * @throws UndefinedTypeException
     * @return static
     */
    abstract public static function fromFloat(float $value);

    /**
     * @throws UndefinedTypeException
     * @return static
     */
    abstract public static function fromInt(int $value);

    /**
     * @return static
     * @param null $value
     */
    abstract public static function fromNull($value);

    /**
     * @throws UndefinedTypeException
     * @return static
     */
    abstract public static function fromString(string $value);

    /**
     * @throws UndefinedTypeException
     * @return never
     */
    abstract public function toBool();

    /**
     * @throws UndefinedTypeException
     * @return never
     */
    abstract public function toDecimal();

    /**
     * @throws UndefinedTypeException
     * @return never
     */
    abstract public function toFloat();

    /**
     * @throws UndefinedTypeException
     * @return never
     */
    abstract public function toInt();

    /**
     * @return null
     */
    abstract public function toNull();

    /**
     * @throws UndefinedTypeException
     */
    abstract public function toString(): string;

    /**
     * @return \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract|static
     */
    abstract public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @return \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract|static
     */
    abstract public static function tryFromDecimal(
        string $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @return \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract|static
     */
    abstract public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @return \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract|static
     */
    abstract public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @return \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract|static
     * @param mixed $value
     */
    abstract public static function tryFromMixed(
        $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @return \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract|static
     */
    abstract public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @throws UndefinedTypeException
     */
    abstract public function value(): string;

    /**
     * @throws UndefinedTypeException
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
