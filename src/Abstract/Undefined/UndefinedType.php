<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Undefined;

use PhpTypedValues\Abstract\TypeInterface;
use PhpTypedValues\Exception\UndefinedTypeException;

/**
 * Special type for "Undefined" \ "Unknown" state.
 * Use it to return type hints when you don't know the value.
 * Use it instead of NULL (could mean anything) to make your code more readable.
 *
 * @psalm-immutable
 */
abstract readonly class UndefinedType implements TypeInterface, UndefinedTypeInterface
{
    public static function create(): static
    {
        return new static();
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toString(): void
    {
        throw new UndefinedTypeException('Undefined type cannot be converted to string.');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function value(): void
    {
        throw new UndefinedTypeException('Undefined type has no value.');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function __toString(): string
    {
        throw new UndefinedTypeException('Undefined type cannot be converted to string.');
    }
}
