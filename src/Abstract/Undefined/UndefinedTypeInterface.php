<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Undefined;

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

    public function value(): never;

    public function toInt(): never;

    public function toFloat(): never;
}
