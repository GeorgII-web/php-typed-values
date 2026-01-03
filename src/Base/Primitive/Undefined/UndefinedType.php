<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Undefined;

use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Undefined\Alias\Undefined;

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
abstract class UndefinedType extends PrimitiveType implements UndefinedTypeInterface
{
    abstract public function value(): string;

    /**
     * @return static
     */
    abstract public static function fromString(string $value);

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     * @param mixed $value
     */
    abstract public static function tryFromMixed(
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
    abstract public static function tryFromString(
        string $value,
        PrimitiveType $default = null
    );
}
