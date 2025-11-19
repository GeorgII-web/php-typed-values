<?php

declare(strict_types=1);

namespace PhpTypedValues\Code\Integer;

/**
 * @psalm-immutable
 */
interface IntTypeInterface
{
    public function value(): int;

    public static function fromInt(int $value): self;

    public function toString(): string;

    public static function fromString(string $value): self;
}
