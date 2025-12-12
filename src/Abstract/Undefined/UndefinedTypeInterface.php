<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Undefined;

use PhpTypedValues\Exception\UndefinedTypeException;

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
