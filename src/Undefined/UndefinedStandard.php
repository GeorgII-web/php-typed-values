<?php

declare(strict_types=1);

namespace PhpTypedValues\Undefined;

use PhpTypedValues\Abstract\Primitive\Undefined\UndefinedType;
use PhpTypedValues\Exception\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Base implementation for a special "UndefinedStandard" typed value.
 *
 * Use it in APIs that must return a typed value when no meaningful value is available yet.
 * Prefer this over null to make intent explicit and keep type-safety.
 *
 * Example
 *  - return UndefinedStandard::create();
 *  - $v->toString(); // throws UndefinedTypeException
 *
 * @psalm-immutable
 */
readonly class UndefinedStandard extends UndefinedType
{
    public static function create(): static
    {
        return new static();
    }

    public static function tryFromMixed(mixed $value): Undefined
    {
        return Undefined::create();
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
