<?php

declare(strict_types=1);

namespace PhpTypedValues\Undefined;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\Undefined\UndefinedTypeAbstract;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

use function is_null;

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
     *
     * @throws UndefinedTypeException
     */
    public static function fromBool(bool $value): static
    {
        throw new UndefinedTypeException('Undefined type cannot be created from boolean');
    }

    /**
     * @psalm-pure
     *
     * @throws UndefinedTypeException
     */
    public static function fromDecimal(string $value): static
    {
        throw new UndefinedTypeException('Undefined type cannot be created from decimal');
    }

    /**
     * @psalm-pure
     *
     * @throws UndefinedTypeException
     */
    public static function fromFloat(float $value): static
    {
        throw new UndefinedTypeException('Undefined type cannot be created from float');
    }

    /**
     * @psalm-pure
     *
     * @throws UndefinedTypeException
     */
    public static function fromInt(int $value): static
    {
        throw new UndefinedTypeException('Undefined type cannot be created from integer');
    }

    /**
     * @psalm-pure
     */
    public static function fromNull(null $value): static
    {
        return new static();
    }

    /**
     * @psalm-pure
     *
     * @throws UndefinedTypeException
     */
    public static function fromString(string $value): static
    {
        throw new UndefinedTypeException('Undefined type cannot be created from string');
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

    /**
     * @psalm-assert-if-true Undefined $this
     */
    public function isUndefined(): bool
    {
        return true;
    }

    public function jsonSerialize(): null
    {
        return $this->toNull();
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toArray(): never
    {
        throw new UndefinedTypeException('Undefined type cannot be converted to array');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toBool(): never
    {
        throw new UndefinedTypeException('Undefined type cannot be converted to boolean');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toDecimal(): string
    {
        throw new UndefinedTypeException('Undefined type cannot be converted to decimal');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toFloat(): never
    {
        throw new UndefinedTypeException('Undefined type cannot be converted to float');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toInt(): never
    {
        throw new UndefinedTypeException('Undefined type cannot be converted to integer');
    }

    public function toNull(): null
    {
        return null;
    }

    /**
     * @throws UndefinedTypeException
     */
    public function toString(): string
    {
        throw new UndefinedTypeException('Undefined type cannot be converted to string');
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromBool($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromDecimal(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromDecimal($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromFloat($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromInt($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return match (true) {
                is_null($value) => static::fromNull($value),
                default => throw new TypeException('Value cannot be casted to undefined type'),
            };
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @throws UndefinedTypeException
     */
    public function value(): string
    {
        throw new UndefinedTypeException('Undefined type has no value');
    }

    /**
     * @throws UndefinedTypeException
     */
    public function __toString(): string
    {
        throw new UndefinedTypeException('Undefined type cannot be converted to string');
    }
}
