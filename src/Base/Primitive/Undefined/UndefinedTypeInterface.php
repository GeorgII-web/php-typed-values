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
    public static function create(): static;

    public static function fromArray(array $value): static;

    public static function fromBool(bool $value): static;

    public static function fromDecimal(string $value): static;

    public static function fromFloat(float $value): static;

    public static function fromInt(int $value): static;

    public static function fromString(string $value): static;

    public function isTypeOf(string ...$classNames): bool;

    /**
     * @throws UndefinedTypeException
     */
    public function toArray(): never;

    /**
     * @throws UndefinedTypeException
     */
    public function toBool(): never;

    /**
     * @throws UndefinedTypeException
     */
    public function toDecimal(): string;

    /**
     * @throws UndefinedTypeException
     */
    public function toFloat(): never;

    /**
     * @throws UndefinedTypeException
     */
    public function toInt(): never;

    /**
     * @throws UndefinedTypeException
     */
    public function toString(): string;

    public static function tryFromArray(
        array $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static;

    public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static;

    public static function tryFromDecimal(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static;

    public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static;

    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static;

    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static;

    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static;

    /**
     * @throws UndefinedTypeException
     */
    public function value(): string;
}
