<?php

declare(strict_types=1);

namespace PhpTypedValues\Code\String;

/**
 * @psalm-immutable
 */
interface StrTypeInterface
{
    public function value(): string;

    public static function fromString(string $value): self;

    public function toString(): string;
}
