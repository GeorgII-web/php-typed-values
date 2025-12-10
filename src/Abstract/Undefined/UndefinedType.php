<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Undefined;

use PhpTypedValues\Abstract\AbstractType;
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
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract readonly class UndefinedType extends AbstractType implements UndefinedTypeInterface
{
    public static function create(): static
    {
        return new static();
    }

    public static function fromString(string $value): static
    {
        return new static();
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toInt(): never
    {
        throw new UndefinedTypeException('UndefinedType cannot be converted to integer.');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toFloat(): never
    {
        throw new UndefinedTypeException('UndefinedType cannot be converted to float.');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toString(): never
    {
        throw new UndefinedTypeException('UndefinedType cannot be converted to string.');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function value(): never
    {
        throw new UndefinedTypeException('UndefinedType has no value.');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function __toString(): string
    {
        throw new UndefinedTypeException('UndefinedType cannot be converted to string.');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function jsonSerialize(): never
    {
        throw new UndefinedTypeException('UndefinedType cannot be serialized for Json.');
    }
}
