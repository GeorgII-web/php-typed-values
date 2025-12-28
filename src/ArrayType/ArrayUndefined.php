<?php

declare(strict_types=1);

namespace PhpTypedValues\ArrayType;

use PhpTypedValues\Base\ArrayType\ArrayType;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Base\Primitive\Undefined\UndefinedTypeInterface;
use PhpTypedValues\Exception\ArrayUndefinedTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Immutable undefined collection.
 *
 * @extends ArrayType<never>
 *
 * @psalm-immutable
 */
readonly class ArrayUndefined extends ArrayType implements UndefinedTypeInterface
{
    public static function fromArray(array $value): static
    {
        return new static();
    }

    /**
     * @throws ArrayUndefinedTypeException
     */
    public function value(): never
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
    public function toArray(): never
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
     * @psalm-return never
     *
     * @throws ArrayUndefinedTypeException
     */
    public function getDefinedItems(): never
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

    public static function tryFromMixed(mixed $value, PrimitiveType $default = new Undefined()): static|PrimitiveType
    {
        return new static();
    }

    public static function tryFromString(string $value, PrimitiveType $default = new Undefined()): static|PrimitiveType
    {
        return new static();
    }
}
