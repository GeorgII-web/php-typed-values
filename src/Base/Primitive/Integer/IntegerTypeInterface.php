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
    public static function fromBool(bool $value): static;

    public static function fromDecimal(string $value): static;

    public static function fromFloat(float $value): static;

    public static function fromInt(int $value): static;

    public static function fromString(string $value): static;

    public function isTypeOf(string ...$classNames): bool;

    public function toBool(): bool;

    public function toDecimal(): string;

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
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromDecimal(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract;

    public function value(): int;
}
