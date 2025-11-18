<?php

declare(strict_types=1);

namespace PhpTypedValues\Contract;

/**
 * @psalm-immutable
 */
interface IntTypeInterface
{
    public function value(): int;

    public static function fromString(string $value): self;

    public static function fromInt(int $value): self;

    public function assert(int $value): void;
}
