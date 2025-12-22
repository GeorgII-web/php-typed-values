<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Shared;

/**
 * Base contract for Array methods.
 *
 * @psalm-immutable
 */
interface ValueObjectArrayInterface
{
    public function toArray(): array;

    public static function fromArray(array $value): self;
}
