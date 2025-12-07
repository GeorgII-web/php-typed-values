<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Integer;

use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * @psalm-immutable
 */
interface IntTypeInterface
{
    public function value(): int;

    public static function fromString(string $value): static;

    public static function fromInt(int $value): static;

    public static function tryFromString(string $value): static|Undefined;

    public static function tryFromInt(int $value): static|Undefined;

    public function toString(): string;

    public function __toString(): string;
}
