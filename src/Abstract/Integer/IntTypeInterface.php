<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Integer;

/**
 * @psalm-immutable
 */
interface IntTypeInterface
{
    public function value(): int;

    public static function fromInt(int $value): static;

    public function toString(): string;

    public static function fromString(string $value): static;

    public function __toString(): string;
}
