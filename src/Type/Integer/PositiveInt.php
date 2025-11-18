<?php

declare(strict_types=1);

namespace PhpTypedValues\Type\Integer;

use Override;
use PhpTypedValues\BaseType\BaseIntType;
use PhpTypedValues\Exception\IntegerTypeException;

/**
 * @psalm-immutable
 */
final readonly class PositiveInt extends BaseIntType
{
    /**
     * @throws IntegerTypeException
     */
    #[Override]
    public function assert(int $value): void
    {
        if ($value <= 0) {
            throw new IntegerTypeException('Value must be a positive integer');
        }
    }
}
