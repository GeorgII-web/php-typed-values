<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Float;

use PhpTypedValues\Base\Primitive\PrimitiveTypeInterface;
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
interface FloatTypeInterface extends PrimitiveTypeInterface
{
    public function value(): float;

    public static function fromFloat(float $value): static;
}
