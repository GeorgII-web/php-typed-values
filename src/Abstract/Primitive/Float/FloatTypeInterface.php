<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Primitive\Float;

use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Contract for float-typed values.
 *
 * Declares the API for float-backed value objects, including creation from
 * native float or validated string, and formatting helpers.
 *
 * Example
 *  - $v = MyFloat::fromFloat(1.5);
 *  - $v->toString(); // "1.5"
 *
 * @psalm-immutable
 */
interface FloatTypeInterface
{
    public function value(): float;

    /**
     * @return static
     */
    public static function fromFloat(float $value);

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
