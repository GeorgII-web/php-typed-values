<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Bool;

/**
 * @psalm-immutable
 */
interface BoolTypeInterface
{
    public function value(): bool;

    public static function fromString(string $value): static;

    public static function fromBool(bool $value): static;

    public function toString(): string;
}
