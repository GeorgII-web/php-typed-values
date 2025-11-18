<?php

declare(strict_types=1);

namespace GeorgiiWeb\PhpTypedValues\Types\Integer;

use InvalidArgumentException;

/**
 * @extends IntType
 *
 * @psalm-immutable
 * @psalm-suppress UnusedClass Public API type that may be used by consumers outside of analyzed paths.
 */
class NonNegativeInt extends IntType
{
    protected function assertValid(mixed $value): void
    {
        parent::assertValid($value);
        if ($value < 0) {
            throw new InvalidArgumentException('Value must be a non-negative integer');
        }
    }
}
