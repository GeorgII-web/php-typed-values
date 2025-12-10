<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract;

use JsonSerializable;
use PhpTypedValues\Exception\TypeException;
use Stringable;

use function is_scalar;

/**
 * Base class for immutable typed values.
 *
 * Responsibilities
 *  - Define a common root for value objects in this library.
 *  - Enforce immutability and a unified interface via {@see AbstractTypeInterface}.
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
abstract readonly class AbstractType implements AbstractTypeInterface, JsonSerializable
{
    /**
     * Safely attempts to convert a mixed value to a string.
     * Returns null if conversion is impossible (array, resource, non-stringable object).
     *
     * @throws TypeException
     */
    public static function convertMixedToString(mixed $value): string
    {
        if (is_scalar($value) || $value === null) {
            return (string) $value;
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        throw new TypeException('Value cannot be cast to string');
    }
}
