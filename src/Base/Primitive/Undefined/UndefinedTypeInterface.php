<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Undefined;

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Contract for the special Undefined typed value.
 *
 * Represents an explicit absence of a meaningful value.
 * Methods that would normally expose a value or its string representation
 * intentionally throw to prevent accidental usage.
 *
 * Example
 *  - $u = Undefined::create();
 *  - $u->value(); // throws UndefinedTypeException
 *
 * @psalm-immutable
 */
interface UndefinedTypeInterface
{
    /**
     * @return static
     */
    public static function create();

    /**
     * @return static
     */
    public static function fromArray(array $value);

    /**
     * @return static
     */
    public static function fromBool(bool $value);

    /**
     * @return static
     */
    public static function fromDecimal(string $value);

    /**
     * @return static
     */
    public static function fromFloat(float $value);

    /**
     * @return static
     */
    public static function fromInt(int $value);

    /**
     * @return static
     */
    public static function fromString(string $value);

    public function isTypeOf(string ...$classNames): bool;

    /**
     * @throws UndefinedTypeException
     * @return never
     */
    public function toArray();

    /**
     * @throws UndefinedTypeException
     * @return never
     */
    public function toBool();

    /**
     * @throws UndefinedTypeException
     */
    public function toDecimal(): string;

    /**
     * @throws UndefinedTypeException
     * @return never
     */
    public function toFloat();

    /**
     * @throws UndefinedTypeException
     * @return never
     */
    public function toInt();

    /**
     * @throws UndefinedTypeException
     */
    public function toString(): string;

    /**
     * @return static
     */
    public static function tryFromArray(
        array $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @return static
     */
    public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @return static
     */
    public static function tryFromDecimal(
        string $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @return static
     */
    public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @return static
     */
    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @param mixed $value
     * @return static
     */
    public static function tryFromMixed(
        $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @return static
     */
    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @throws UndefinedTypeException
     */
    public function value(): string;
}
