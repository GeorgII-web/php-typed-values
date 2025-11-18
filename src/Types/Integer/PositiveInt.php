<?php

declare(strict_types=1);

namespace GeorgiiWeb\PhpTypedValues\Types\Integer;

use InvalidArgumentException;

/**
 * @extends IntType
 *
 * @psalm-immutable
 */
class PositiveInt extends IntType
{
    protected function assertValid(mixed $value): void
    {
        parent::assertValid($value);
        if ($value <= 0) {
            throw new InvalidArgumentException('Value must be a positive integer');
        }
    }
}
