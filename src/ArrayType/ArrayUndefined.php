<?php

declare(strict_types=1);

namespace PhpTypedValues\ArrayType;

use PhpTypedValues\Base\ArrayType\ArrayTypeAbstract;
use PhpTypedValues\Exception\ArrayType\UndefinedArrayTypeException;

/**
 * Immutable undefined collection.
 *
 * @extends ArrayTypeAbstract<never>
 *
 * @psalm-immutable
 */
class ArrayUndefined extends ArrayTypeAbstract
{
    /**
     * @throws UndefinedArrayTypeException
     * @return never
     */
    public function count()
    {
        throw new UndefinedArrayTypeException('Undefined array has no items to count');
    }

    /**
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * @psalm-pure
     * @return static
     */
    public static function fromArray(array $value)
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

    /**
     * @return never
     */
    public function getIterator()
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
     * @return never
     */
    public function jsonSerialize()
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
     * @return never
     */
    public function toFloat()
    {
        throw new UndefinedArrayTypeException('Undefined array cannot be converted to float');
    }

    /**
     * @throws UndefinedArrayTypeException
     * @return never
     */
    public function toInt()
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
