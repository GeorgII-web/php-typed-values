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
class ArrayUndefined extends ArrayType implements UndefinedTypeInterface
{
    /**
     * @return static
     */
    public static function fromArray(array $value)
    {
        return new static();
    }

    /**
     * @throws ArrayUndefinedTypeException
     * @return never
     */
    public function value()
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
     * @return never
     */
    public function toArray()
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
     * @psalm-return never
     *
     * @throws ArrayUndefinedTypeException
     * @return never
     */
    public function getDefinedItems()
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

    /**
     * @return static|\PhpTypedValues\Base\Primitive\PrimitiveType
     * @param mixed $value
     */
    public static function tryFromMixed($value, PrimitiveType $default = null)
    {
        $default ??= new Undefined();
        return new static();
    }

    /**
     * @return static|\PhpTypedValues\Base\Primitive\PrimitiveType
     */
    public static function tryFromString(string $value, PrimitiveType $default = null)
    {
        $default ??= new Undefined();
        return new static();
    }
}
