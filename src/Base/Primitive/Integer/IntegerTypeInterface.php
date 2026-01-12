<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Integer;

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Contract for integer-typed values.
 *
 * Declares the API for int-backed value objects, including strict parsing
 * from string/native int and formatting helpers.
 *
 * Example
 *  - $v = MyInt::fromInt(7);
 *  - $v->toString(); // "7"
 *
 * @psalm-immutable
 */
interface IntegerTypeInterface
{
    /**
     * @return static
     */
    public static function fromBool(bool $value);

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

    public function toBool(): bool;

    public function toFloat(): float;

    public function toInt(): int;

    public function toString(): string;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromBool(
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
    public static function tryFromFloat(
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
    public static function tryFromInt(
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
    public static function tryFromMixed(
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
    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = null
    );

    public function value(): int;
}
