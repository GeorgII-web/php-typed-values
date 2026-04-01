<?php

declare(strict_types=1);

namespace PhpTypedValues\ArrayType;

use PhpTypedValues\Base\ArrayType\ArrayTypeAbstract;
use PhpTypedValues\Base\ArrayType\ArrayUndefinedTypeInterface;
use PhpTypedValues\Exception\ArrayType\UndefinedArrayTypeException;

/**
 * Immutable undefined collection.
 *
 * @extends ArrayTypeAbstract<never>
 *
 * @psalm-immutable
 */
readonly class ArrayUndefined extends ArrayTypeAbstract implements ArrayUndefinedTypeInterface
{
    /**
     * @throws UndefinedArrayTypeException
     */
    public function count(): never
    {
        throw new UndefinedArrayTypeException('Undefined array has no items to count');
    }

    public static function create(): static
    {
        return new static();
    }

    /**
     * @psalm-pure
     */
    public static function fromArray(array $value): static
    {
        return new static();
    }

    /**
     * @psalm-suppress PossiblyUnusedReturnValue
     *
     * @throws UndefinedArrayTypeException
     */
    public function getDefinedItems(): array
    {
        throw new UndefinedArrayTypeException('Undefined array has no defined items');
    }

    public function getIterator(): never
    {
        throw new UndefinedArrayTypeException('Undefined array has no items for iterator');
    }

    public function hasUndefined(): bool
    {
        return true;
    }

    public function isEmpty(): bool
    {
        return true;
    }

    public function isTypeOf(string ...$classNames): bool
    {
        foreach ($classNames as $className) {
            if ($this instanceof $className) {
                return true;
            }
        }

        return false;
    }

    public function isUndefined(): bool
    {
        return true;
    }

    /**
     * @throws UndefinedArrayTypeException
     */
    public function jsonSerialize(): never
    {
        throw new UndefinedArrayTypeException('Undefined array cannot be converted to Json');
    }

    /**
     * @throws UndefinedArrayTypeException
     */
    public function toArray(): array
    {
        throw new UndefinedArrayTypeException('Undefined array cannot be converted to array');
    }

    /**
     * @throws UndefinedArrayTypeException
     */
    public function toFloat(): never
    {
        throw new UndefinedArrayTypeException('Undefined array cannot be converted to float');
    }

    /**
     * @throws UndefinedArrayTypeException
     */
    public function toInt(): never
    {
        throw new UndefinedArrayTypeException('Undefined array cannot be converted to integer');
    }

    /**
     * @throws UndefinedArrayTypeException
     */
    public function value(): array
    {
        throw new UndefinedArrayTypeException('Undefined array has no value');
    }
}
