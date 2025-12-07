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

    public function value(): void;

    public function toInt(): void;

    public function toFloat(): void;

    public function toString(): void;

    public function __toString(): string;
}
