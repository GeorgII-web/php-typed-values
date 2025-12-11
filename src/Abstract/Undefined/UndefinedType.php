<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Undefined;

use PhpTypedValues\Abstract\AbstractType;
use PhpTypedValues\Exception\UndefinedTypeException;

/**
 * Base implementation for a special "Undefined/Unknown" typed value.
 *
 * Use it in APIs that must return a typed value when no meaningful value is available yet.
 * Prefer this over null to make intent explicit and keep type-safety.
 *
 * Example
 *  - return Undefined::create();
 *  - $v->toString(); // throws UndefinedTypeException
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract class UndefinedType extends AbstractType implements UndefinedTypeInterface
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
    public function toString()
    {
        throw new UndefinedTypeException('UndefinedType cannot be converted to string.');
    }

    /**
     * @throws UndefinedTypeException
     * @return never
     */
    public function value()
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
}
