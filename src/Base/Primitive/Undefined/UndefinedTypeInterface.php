<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Undefined;

use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\UndefinedTypeException;
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

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType;

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType;

    /**
     * @throws UndefinedTypeException
     */
    public function toInt(): never;

    /**
     * @throws UndefinedTypeException
     */
    public function toFloat(): never;

    /**
     * @throws UndefinedTypeException
     */
    public function value(): never;
}
