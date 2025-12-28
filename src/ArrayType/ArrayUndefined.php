<?php

declare(strict_types=1);

namespace PhpTypedValues\ArrayType;

use PhpTypedValues\Base\ArrayType\ArrayType;
use PhpTypedValues\Base\ArrayType\ArrayUndefinedTypeInterface;
use PhpTypedValues\Exception\ArrayUndefinedTypeException;

/**
 * Immutable undefined collection.
 *
 * @extends ArrayType<never>
 *
 * @psalm-immutable
 */
readonly class ArrayUndefined extends ArrayType implements ArrayUndefinedTypeInterface
{
    public static function fromArray(array $value): static
    {
        return new static();
    }

    /**
     * @throws ArrayUndefinedTypeException
     */
    public function value(): array
    {
        throw new ArrayUndefinedTypeException('Undefined array has no value');
    }

    public function getIterator(): never
    {
        throw new ArrayUndefinedTypeException('Undefined array has no items for iterator');
    }

    /**
     * @throws ArrayUndefinedTypeException
     */
    public function count(): never
    {
        throw new ArrayUndefinedTypeException('Undefined array has no items to count');
    }

    /**
     * @throws ArrayUndefinedTypeException
     */
    public function toArray(): array
    {
        throw new ArrayUndefinedTypeException('Undefined array cannot be converted to array');
    }

    /**
     * @throws ArrayUndefinedTypeException
     */
    public function jsonSerialize(): never
    {
        throw new ArrayUndefinedTypeException('Undefined array cannot be converted to Json');
    }

    public function isEmpty(): bool
    {
        return true;
    }

    public function isUndefined(): bool
    {
        return true;
    }

    public function hasUndefined(): bool
    {
        return true;
    }

    /**
     * @psalm-suppress PossiblyUnusedReturnValue
     *
     * @throws ArrayUndefinedTypeException
     */
    public function getDefinedItems(): array
    {
        throw new ArrayUndefinedTypeException('Undefined array has no defined items');
    }

    public static function create(): static
    {
        return new static();
    }

    /**
     * @throws ArrayUndefinedTypeException
     */
    public function toInt(): never
    {
        throw new ArrayUndefinedTypeException('Undefined array cannot be converted to integer');
    }

    /**
     * @throws ArrayUndefinedTypeException
     */
    public function toFloat(): never
    {
        throw new ArrayUndefinedTypeException('Undefined array cannot be converted to float');
    }
}
