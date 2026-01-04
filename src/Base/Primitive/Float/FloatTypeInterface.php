<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Float;

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Contract for float-typed values.
 *
 * Declares the API for float-backed value objects, including creation from
 * native float or validated string, and formatting helpers.
 *
 * Example
 *  - $v = MyFloat::fromFloat(1.5);
 *  - $v->toString(); // "1.5"
 *
 * @psalm-immutable
 */
interface FloatTypeInterface
{
    /**
     * Create an instance from a validated string representation.
     *
     * Implementations should perform strict validation and may throw a
     * domain-specific subtype of {@see TypeException}
     * when the provided value is invalid.
     *
     * @throws TypeException
     */
    public static function fromString(string $value): static;

    public static function fromFloat(float $value): static;

    public function value(): float;

    public function isTypeOf(string ...$classNames): bool;

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
}
