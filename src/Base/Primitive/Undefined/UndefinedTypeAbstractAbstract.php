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
abstract readonly class UndefinedTypeAbstractAbstract extends PrimitiveTypeAbstract implements UndefinedTypeInterface
{
    abstract public static function create(): static;

    abstract public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static;

    abstract public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static;

    abstract public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static;

    abstract public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static;

    abstract public static function tryFromArray(
        array $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static;

    abstract public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static;

    abstract public static function fromBool(bool $value): static;

    abstract public static function fromFloat(float $value): static;

    abstract public static function fromInt(int $value): static;

    abstract public static function fromString(string $value): static;

    abstract public static function fromArray(array $value): static;

    /**
     * @throws UndefinedTypeException
     */
    abstract public function toInt(): never;

    /**
     * @throws UndefinedTypeException
     */
    abstract public function toFloat(): never;

    /**
     * @throws UndefinedTypeException
     */
    abstract public function toBool(): never;

    /**
     * @throws UndefinedTypeException
     */
    abstract public function toString(): string;

    /**
     * @throws UndefinedTypeException
     */
    abstract public function toArray(): never;

    /**
     * @throws UndefinedTypeException
     */
    abstract public function value(): string;
}
