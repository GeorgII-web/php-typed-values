<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive;

use JsonSerializable;
use PhpTypedValues\Base\TypeInterface;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Base contract for all immutable typed values in this library.
 *
 * Responsibilities
 *  - Provide a lossless string representation via {@see toString} and {@see __toString}.
 *  - Concrete implementations may also provide tolerant factories like
 *    `tryFromMixed(mixed): static|Undefined` that return {@see Undefined} on failure.
 *
 * Notes
 *  - All implementations MUST be immutable.
 *
 * @psalm-immutable
 */
interface PrimitiveTypeInterface extends TypeInterface, JsonSerializable
{
    public function value(): mixed;

    /**
     * Checks if the current object is an instance of the provided class names.
     */
    public function isTypeOf(string ...$classNames): bool;

    /**
     * Returns true if the Object value is empty.
     */
    public function isEmpty(): bool;

    /**
     * Returns if the Object value is an Undefined type class.
     */
    public function isUndefined(): bool;

    /**
     * Returns a normalized string representation of the underlying value.
     */
    public function toString(): string;

    /**
     * Alias of {@see toString} for convenient casting.
     */
    public function __toString(): string;

    /**
     * JSON representation of the value.
     *
     * Marked as mutation-free so Psalm treats calls as pure in immutable contexts.
     *
     * @psalm-mutation-free
     */
    public function jsonSerialize(): mixed;
}
