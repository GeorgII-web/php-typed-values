<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\String;

use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * @psalm-immutable
 */
interface StrTypeInterface
{
    public function value(): string;

    public static function fromString(string $value): static;

    public static function tryFromString(string $value): static|Undefined;

    public function toString(): string;

    public function __toString(): string;
}
