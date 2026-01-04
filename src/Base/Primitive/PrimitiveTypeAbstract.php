<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive;

/**
 * Base class for immutable typed values.
 *
 * Responsibilities
 *  - Define a common root for value objects in this library.
 *  - Enforce immutability and a unified interface via {@see PrimitiveTypeInterface}.
 *  - Mark all descendants as implementing {@see JsonSerializable} — concrete
 *    classes SHOULD implement `jsonSerialize()` consistently with their
 *    `toString()`/`value()` representation.
 *
 * Notes
 *  - This class does not provide storage or behavior by itself; concrete
 *    subclasses define validation, factories (e.g. `fromString()`), and accessors
 *    (e.g. `value()` and formatting helpers).
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract readonly class PrimitiveTypeAbstract implements PrimitiveTypeInterface
{
    /**
     * Checks if the current object (or its parents) is an instance of the provided class names.
     */
    abstract public function isTypeOf(string ...$classNames): bool;

    /**
     * Returns true if the Object value is empty.
     */
    abstract public function isEmpty(): bool;

    /**
     * Returns if the Object value is an Undefined type class.
     */
    abstract public function isUndefined(): bool;

    /**
     * Returns a normalized string representation of the underlying value.
     */
    abstract public function toString(): string;

    /**
     * Alias of {@see toString} for convenient casting.
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * JSON representation of the value.
     *
     * Marked as mutation-free so Psalm treats calls as pure in immutable contexts.
     *
     * @psalm-mutation-free
     */
    abstract public function jsonSerialize(): mixed;
}
