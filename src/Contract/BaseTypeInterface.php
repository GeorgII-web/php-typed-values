<?php

declare(strict_types=1);

namespace PhpTypedValues\Contract;

/**
 * @psalm-immutable
 */
interface BaseTypeInterface
{
    public function value(): mixed;

    public function toString(): string;
}
