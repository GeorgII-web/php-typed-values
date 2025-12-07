<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Float;

/**
 * @psalm-immutable
 */
interface FloatTypeInterface
{
    public function value(): float;

    public static function fromFloat(float $value): static;

    public function toString(): string;

    public static function fromString(string $value): static;

    public function __toString(): string;
}
