<?php

declare(strict_types=1);

namespace PhpTypedValues\ArrayType;

use PhpTypedValues\Base\ArrayType\ArrayType;
use PhpTypedValues\Exception\UndefinedTypeException;
use Traversable;

/**
 * Immutable undefined collection.
 *
 * @extends ArrayType<never>
 *
 * @psalm-immutable
 */
readonly class ArrayUndefined extends ArrayType
{
    public static function fromArray(array $value): static
    {
        return new static();
    }

    /**
     * @throws UndefinedTypeException
     */
    public function value(): never
    {
        throw new UndefinedTypeException('Undefined array has no value');
    }

    public function getIterator(): Traversable
    {
        throw new UndefinedTypeException('Undefined array has no items for iterator');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function count(): int
    {
        throw new UndefinedTypeException('Undefined array has no items');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toArray(): array
    {
        throw new UndefinedTypeException('Undefined array cannot be converted to array');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function jsonSerialize(): array
    {
        throw new UndefinedTypeException('Undefined array cannot be converted to Json');
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
     * @throws UndefinedTypeException
     */
    public function getDefinedItems(): array
    {
        throw new UndefinedTypeException('Undefined array has no defined items');
    }

    public static function create(): static
    {
        return new static();
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toInt(): never
    {
        throw new UndefinedTypeException('Undefined array cannot be converted to integer');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toFloat(): never
    {
        throw new UndefinedTypeException('Undefined array cannot be converted to float');
    }
}
