<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive;

use PhpTypedValues\Exception\TypeException;
use Stringable;

use function is_bool;
use function is_scalar;

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
abstract readonly class PrimitiveType implements PrimitiveTypeInterface
{
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
    abstract public function __toString(): string;

    /**
     * JSON representation of the value.
     *
     * Marked as mutation-free so Psalm treats calls as pure in immutable contexts.
     *
     * @psalm-mutation-free
     */
    abstract public function jsonSerialize(): mixed;

    /**
     * todo refactor for each type separately.
     *
     * Safely attempts to convert a mixed value to a string.
     * Returns null if conversion is impossible (array, resource, non-stringable object).
     *
     * @throws TypeException
     */
    protected static function convertMixedToString(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if ($value === null) {
            return '';
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        throw new TypeException('Value cannot be cast to string');
    }
}
