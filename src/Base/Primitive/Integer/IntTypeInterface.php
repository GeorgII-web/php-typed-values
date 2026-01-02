<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Integer;

use PhpTypedValues\Base\Primitive\PrimitiveType;
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
interface IntTypeInterface
{
    /**
     * @return static
     */
    public static function fromString(string $value);

    /**
     * @return static
     */
    public static function fromInt(int $value);

    /**
     * @return static
     */
    public static function fromFloat(float $value);

    /**
     * @return static
     */
    public static function fromBool(bool $value);

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromInt(
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
    public static function tryFromMixed(
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
    public static function tryFromString(
        string $value,
        PrimitiveType $default = null
    );

    public function value(): int;

    public function toInt(): int;

    public function toFloat(): float;

    public function toBool(): bool;

    public function toString(): string;
}
