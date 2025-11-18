<?php

declare(strict_types=1);

namespace GeorgiiWeb\PhpTypedValues\Contracts;

/**
 * @psalm-template T
 *
 * @psalm-immutable
 */
interface TypedValueInterface
{
    /**
     * Get the underlying value.
     *
     * @psalm-return T
     */
    public function getValue(): mixed;

    /**
     * Return a new instance with the provided value (immutably).
     *
     * @psalm-param T $value
     *
     * @psalm-suppress PossiblyUnusedMethod This is part of the public API; usages may occur outside of the analyzed codebase.
     */
    public function setValue(mixed $value): static;

    /**
     * String representation of the value object.
     */
    public function toString(): string;

    /**
     * Create an instance from string value.
     *
     * @psalm-param string $value
     */
    public static function fromString(string $value): static;
}
