<?php

declare(strict_types=1);

namespace GeorgiiWeb\PhpTypedValues\Types\Integer;

use GeorgiiWeb\PhpTypedValues\Types\Base\TypedValue;
use InvalidArgumentException;

use function is_int;

/**
 * @extends TypedValue<int>
 *
 * @psalm-immutable
 */
class IntType extends TypedValue
{
    protected function assertValid(mixed $value): void
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException('Value must be integer');
        }
    }

    protected static function castFromString(string $value): mixed
    {
        if (!preg_match('/^-?\d+$/', $value)) {
            throw new InvalidArgumentException('String is not a valid integer');
        }

        return (int) $value;
    }
}
