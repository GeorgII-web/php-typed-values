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
class ArrayUndefined extends ArrayType implements ArrayUndefinedTypeInterface
{
    /**
     * @return static
     */
    public static function fromArray(array $value)
    {
        return new static();
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

    /**
     * @throws ArrayUndefinedTypeException
     */
    public function value(): array
    {
        throw new ArrayUndefinedTypeException('Undefined array has no value');
    }

    /**
     * @return never
     */
    public function getIterator()
    {
        throw new ArrayUndefinedTypeException('Undefined array has no items for iterator');
    }

    /**
     * @throws ArrayUndefinedTypeException
     * @return never
     */
    public function count()
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
     * @return never
     */
    public function jsonSerialize()
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

    /**
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * @throws ArrayUndefinedTypeException
     * @return never
     */
    public function toInt()
    {
        throw new ArrayUndefinedTypeException('Undefined array cannot be converted to integer');
    }

    /**
     * @throws ArrayUndefinedTypeException
     * @return never
     */
    public function toFloat()
    {
        throw new ArrayUndefinedTypeException('Undefined array cannot be converted to float');
    }
}
