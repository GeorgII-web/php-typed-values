<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Float;

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

    public static function fromFloat(float $value): static;

    public function toString(): string;

    public static function fromString(string $value): static;

    public static function tryFromString(string $value): static|Undefined;

    public static function tryFromFloat(float $value): static|Undefined;

    public function __toString(): string;
}
