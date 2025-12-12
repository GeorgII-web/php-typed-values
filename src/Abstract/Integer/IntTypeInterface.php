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

    public static function fromInt(int $value): static;

    public static function tryFromInt(int $value): static|Undefined;
}
