<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Undefined;

use PhpTypedValues\Abstract\TypeInterface;
use PhpTypedValues\Exception\UndefinedTypeException;

/**
 * Base implementation for a special "Undefined/Unknown" typed value.
 *
 * Use it in APIs that must return a typed value when no meaningful value is available yet.
 * Prefer this over null to make intent explicit and keep type-safety.
 *
 * Example
 *  - return Undefined::create();
 *  - $v->toString(); // throws UndefinedTypeException
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
    public function toInt(): void
    {
        throw new UndefinedTypeException('Undefined type cannot be converted to integer.');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toFloat(): void
    {
        throw new UndefinedTypeException('Undefined type cannot be converted to float.');
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
