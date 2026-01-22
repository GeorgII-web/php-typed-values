<?php

declare(strict_types=1);

namespace PhpTypedValues\Undefined;

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\Undefined\UndefinedTypeAbstract;
use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
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
readonly class UndefinedStandard extends UndefinedTypeAbstract
{
    /**
     * @psalm-pure
     */
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
     * @psalm-pure
     */
    public static function fromBool(bool $value): static
    {
        return new static();
    }

    /**
     * @psalm-pure
     */
    public static function fromFloat(float $value): static
    {
        return new static();
    }

    /**
     * @psalm-pure
     */
    public static function fromInt(int $value): static
    {
        return new static();
    }

    /**
     * @psalm-pure
     */
    public static function fromString(string $value): static
    {
        return new static();
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
     * @throws UndefinedTypeException
     */
    public function jsonSerialize(): never
    {
        throw new UndefinedTypeException('UndefinedType cannot be serialized for Json.');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toArray(): never
    {
        throw new UndefinedTypeException('UndefinedType cannot be converted to array.');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toBool(): never
    {
        throw new UndefinedTypeException('UndefinedType cannot be converted to boolean.');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toFloat(): never
    {
        throw new UndefinedTypeException('UndefinedType cannot be converted to float.');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toInt(): never
    {
        throw new UndefinedTypeException('UndefinedType cannot be converted to integer.');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toString(): string
    {
        throw new UndefinedTypeException('UndefinedType cannot be converted to string.');
    }

    /**
     * @psalm-pure
     */
    public static function tryFromArray(
        array $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static {
        return static::fromArray($value);
    }

    /**
     * @psalm-pure
     */
    public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static {
        return static::fromBool($value);
    }

    /**
     * @psalm-pure
     */
    public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static {
        return static::fromFloat($value);
    }

    /**
     * @psalm-pure
     */
    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static {
        return static::fromInt($value);
    }

    /**
     * @psalm-pure
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static {
        return new static();
    }

    /**
     * @psalm-pure
     */
    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static {
        return static::fromString($value);
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
}
