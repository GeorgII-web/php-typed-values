<?php

declare(strict_types=1);

namespace PhpTypedValues\Undefined;

use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Base\Primitive\Undefined\UndefinedType;
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
class UndefinedStandard extends UndefinedType
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    public static function tryFromString(
        string $value,
        PrimitiveType $default = null
    ): Undefined {
        $default ??= new Undefined();
        return Undefined::create();
    }

    /**
     * @param mixed $value
     */
    public static function tryFromMixed(
        $value,
        PrimitiveType $default = null
    ): Undefined {
        $default ??= new Undefined();
        return Undefined::create();
    }

    /**
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static();
    }

    /**
     * @throws UndefinedTypeException
     * @return never
     */
    public function toInt()
    {
        throw new UndefinedTypeException('UndefinedType cannot be converted to integer.');
    }

    /**
     * @throws UndefinedTypeException
     * @return never
     */
    public function toFloat()
    {
        throw new UndefinedTypeException('UndefinedType cannot be converted to float.');
    }

    /**
     * @throws UndefinedTypeException
     * @return never
     */
    public function toArray()
    {
        throw new UndefinedTypeException('UndefinedType cannot be converted to array.');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toString(): string
    {
        throw new UndefinedTypeException('UndefinedType cannot be converted to string.');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function value(): string
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
     * @return never
     */
    public function jsonSerialize()
    {
        throw new UndefinedTypeException('UndefinedType cannot be serialized for Json.');
    }

    public function isEmpty(): bool
    {
        return true;
    }

    public function isUndefined(): bool
    {
        return true;
    }
}
