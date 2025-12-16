<?php

declare(strict_types=1);

namespace PhpTypedValues\Internal\Primitive;

use JsonSerializable;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Internal\Shared\IsEmptyInterface;
use PhpTypedValues\Internal\TypeInterface;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Base contract for all immutable typed values in this library.
 *
 * Responsibilities
 *  - Provide strict construction from a validated string via {@see fromString}.
 *  - Provide a lossless string representation via {@see toString} and {@see __toString}.
 *  - Concrete implementations may also provide tolerant factories like
 *    `tryFromMixed(mixed): static|Undefined` that return {@see Undefined} on failure.
 *
 * Notes
 *  - All implementations MUST be immutable.
 *
 * @psalm-immutable
 */
interface PrimitiveTypeInterface extends TypeInterface, JsonSerializable, IsEmptyInterface
{
    /**
     * Create an instance from a validated string representation.
     *
     * Implementations should perform strict validation and may throw a
     * domain-specific subtype of {@see TypeException}
     * when the provided value is invalid.
     *
     * @throws TypeException
     */
    public static function fromString(string $value): static;

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
