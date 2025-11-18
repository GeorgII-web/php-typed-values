<?php

declare(strict_types=1);

namespace GeorgiiWeb\PhpTypedValues\Contracts;

/**
 * @psalm-immutable
 */
interface BaseTypeInterface
{
    public function value(): mixed;

    public function toString(): string;
}
