<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Bool;

use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\BoolTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Contract for boolean typed values.
 *
 * Describes the API that all bool-backed value objects must implement,
 * including factories, accessors, and formatting helpers.
 *
 * Example
 *  - $v = MyBoolean::fromString('true');
 *  - $v->value(); // true
 *  - (string) $v; // "true"
 *
 * @psalm-immutable
 */
interface BoolTypeInterface
{
    /**
     * Create an instance from a validated string representation.
     *
     * Implementations should perform strict validation and may throw a
     * domain-specific subtype of {@see TypeException}
     * when the provided value is invalid.
     *
     * @throws TypeException
     * @return static
     */
    public static function fromString(string $value);

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        PrimitiveType $default = null
    );

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     * @param mixed $value
     */
    public static function tryFromMixed(
        $value,
        PrimitiveType $default = null
    );

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromInt(
        int $value,
        PrimitiveType $default = null
    );

    /**
     * @throws BoolTypeException
     * @return static
     */
    public static function fromInt(int $value);

    public function value(): bool;

    /**
     * @return static
     */
    public static function fromBool(bool $value);
}
