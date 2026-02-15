<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive;

use JsonSerializable;
use PhpTypedValues\Base\TypeInterface;
use PhpTypedValues\Undefined\Alias\Undefined;
use ReturnTypeWillChange;

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
    // todo add all methods converters
    /**
     * Returns true if the Object value is empty.
     */
    public function isEmpty(): bool;

    /**
     * Checks if the current object (or its parents) is an instance of the provided class names.
     */
    public function isTypeOf(string ...$classNames): bool;

    /**
     * Returns if the Object value is an Undefined type class.
     */
    public function isUndefined(): bool;

    /**
     * JSON representation of the value.
     *
     * Marked as mutation-free so Psalm treats calls as pure in immutable contexts.
     *
     * @psalm-mutation-free
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize();

    /**
     * Returns a normalized string representation of the underlying value.
     */
    public function toString(): string;

    /**
     * @return mixed
     */
    public function value();

    /**
     * Alias of {@see toString} for convenient casting.
     */
    public function __toString(): string;
}
