<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Bool;

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Base implementation for boolean typed values.
 *
 * Provides common formatting helpers and factory methods for bool-backed
 * value objects. Concrete boolean types extend this class and add
 * domain-specific validation if needed.
 *
 * Example
 *  - $v = MyBoolean::fromBool(true);
 *  - $v->toString(); // "true"
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract class BoolTypeAbstract extends PrimitiveTypeAbstract implements BoolTypeInterface
{
    /**
     * @return static
     */
    abstract public static function fromBool(bool $value);

    /**
     * @return static
     */
    abstract public static function fromFloat(float $value);

    /**
     * @return static
     */
    abstract public static function fromInt(int $value);

    /**
     * @return static
     */
    abstract public static function fromString(string $value);

    abstract public function isTypeOf(string ...$classNames): bool;

    abstract public function toBool(): bool;

    abstract public function toFloat(): float;

    abstract public function toInt(): int;

    abstract public function toString(): string;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     * @param mixed $value
     */
    abstract public static function tryFromMixed(
        $value,
        PrimitiveTypeAbstract $default = null
    );

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = null
    );

    abstract public function value(): bool;

    public function __toString(): string
    {
        return $this->toString();
    }
}
