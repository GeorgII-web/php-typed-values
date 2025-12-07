<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Bool;

use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Contract for boolean typed values.
 *
 * Describes the API that all bool-backed value objects must implement,
 * including factories, accessors and formatting helpers.
 *
 * Example
 *  - $v = MyBoolean::fromString('true');
 *  - $v->value();      // true
 *  - (string) $v;      // "true"
 *
 * @psalm-immutable
 */
interface BoolTypeInterface
{
    public function value(): bool;

    public static function tryFromString(string $value): static|Undefined;

    public static function tryFromInt(int $value): static|Undefined;

    public static function fromString(string $value): static;

    public static function fromBool(bool $value): static;

    public function toString(): string;

    public function __toString(): string;
}
