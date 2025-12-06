<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\String;

/**
 * @psalm-immutable
 */
interface StrTypeInterface
{
    public function value(): string;

    public static function fromString(string $value): static;

    public function toString(): string;
}
