<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Shared;

use PhpTypedValues\Exception\TypeException;

/**
 * Base contract to convert string to an object.
 *
 * @psalm-immutable
 */
interface FromString
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
}
