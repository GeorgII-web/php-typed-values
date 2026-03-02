<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Float;

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Undefined\Alias\Undefined;

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
abstract class FloatTypeAbstract extends PrimitiveTypeAbstract implements FloatTypeInterface
{
    /**
     * @return static
     */
    abstract public static function fromBool(bool $value);

    /**
     * @return static
     */
    abstract public static function fromDecimal(string $value);

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

    abstract public function toBool(): bool;

    abstract public function toDecimal(): string;

    abstract public function toFloat(): float;

    abstract public function toInt(): int;

    abstract public function toString(): string;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromDecimal(
        string $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     * @param mixed $value
     */
    abstract public static function tryFromMixed(
        $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = null
    );

    abstract public function value(): float;

    public function __toString(): string
    {
        return $this->toString();
    }
}
