<?php

declare(strict_types=1);

namespace GeorgiiWeb\PhpTypedValues\Types\Integer;

use GeorgiiWeb\PhpTypedValues\Exception\IntegerTypeException;
use GeorgiiWeb\PhpTypedValues\Types\Base\BaseIntType;

/**
 * @psalm-immutable
 */
final class PositiveInt extends BaseIntType
{
    /**
     * @throws IntegerTypeException
     */
    public function assert(int $value): void
    {
        if ($value <= 0) {
            throw new IntegerTypeException('Value must be a positive integer');
        }
    }
}
