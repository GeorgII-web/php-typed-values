<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Integer;

use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Contract for integer-typed values.
 *
 * Declares the API for int-backed value objects, including strict parsing
 * from string/native int and formatting helpers.
 *
 * Example
 *  - $v = MyInt::fromInt(7);
 *  - $v->toString(); // "7"
 *
 * @psalm-immutable
 */
interface IntTypeInterface
{
    public function value(): int;

    /**
     * @return static
     */
    public static function fromInt(int $value);

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromInt(int $value);

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     * @param mixed $value
     */
    public static function tryFromMixed($value);

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value);
}
