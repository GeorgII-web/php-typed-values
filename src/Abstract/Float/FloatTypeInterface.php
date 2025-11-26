<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Float;

/**
 * @psalm-immutable
 */
interface FloatTypeInterface
{
    public function value(): float;

    public static function fromFloat(float $value): self;

    public function toString(): string;

    public static function fromString(string $value): self;
}
