<?php

declare(strict_types=1);

namespace PhpTypedValues\ArrayType;

use PhpTypedValues\Base\ArrayType\ArrayTypeAbstract;
use PhpTypedValues\Base\ArrayType\ArrayUndefinedTypeInterface;
use PhpTypedValues\Exception\ArrayType\ArrayUndefinedTypeException;
use ReturnTypeWillChange;

/**
 * Immutable undefined collection.
 *
 * @extends ArrayTypeAbstract<never>
 *
 * @psalm-immutable
 */
class ArrayUndefined extends ArrayTypeAbstract implements ArrayUndefinedTypeInterface
{
    /**
     * @throws ArrayUndefinedTypeException
     * @return never
     */
    #[ReturnTypeWillChange]
    public function count()
    {
        throw new ArrayUndefinedTypeException('Undefined array has no items to count');
    }

    /**
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * @return static
     */
    public static function fromArray(array $value)
    {
        return new static();
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
     * @return never
     */
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        throw new ArrayUndefinedTypeException('Undefined array has no items for iterator');
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
     * @throws ArrayUndefinedTypeException
     * @return never
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        throw new ArrayUndefinedTypeException('Undefined array cannot be converted to Json');
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
    public function toFloat()
    {
        throw new ArrayUndefinedTypeException('Undefined array cannot be converted to float');
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
     */
    public function value(): array
    {
        throw new ArrayUndefinedTypeException('Undefined array has no value');
    }
}
