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

    /**
     * @return static
     */
    public static function tryFromBool(
        bool $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        return static::fromBool($value);
    }

    /**
     * @return static
     */
    public static function tryFromFloat(
        float $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        return static::fromFloat($value);
    }

    /**
     * @return static
     */
    public static function tryFromInt(
        int $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        return static::fromInt($value);
    }

    /**
     * @return static
     */
    public static function tryFromString(
        string $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        return static::fromString($value);
    }

    /**
     * @return static
     */
    public static function tryFromArray(
        array $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        return static::fromArray($value);
    }

    /**
     * @param mixed $value
     * @return static
     */
    public static function tryFromMixed(
        $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        return new static();
    }

    /**
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static();
    }

    /**
     * @return static
     */
    public static function fromFloat(float $value)
    {
        return new static();
    }

    /**
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static();
    }

    /**
     * @return static
     */
    public static function fromString(string $value)
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
    public function toBool()
    {
        throw new UndefinedTypeException('UndefinedType cannot be converted to boolean.');
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
     * @return never
     */
    public function toArray()
    {
        throw new UndefinedTypeException('UndefinedType cannot be converted to array.');
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
