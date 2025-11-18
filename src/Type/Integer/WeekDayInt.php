<?php

declare(strict_types=1);

namespace PhpTypedValues\Type\Integer;

use Override;
use PhpTypedValues\BaseType\BaseIntType;
use PhpTypedValues\Exception\IntegerTypeException;

/**
 * @psalm-immutable
 */
final readonly class WeekDayInt extends BaseIntType
{
    /**
     * @throws IntegerTypeException
     */
    #[Override]
    public function assert(int $value): void
    {
        $this->assertGreaterThan($value, 1, true);
        $this->assertLessThan($value, 7, true);
    }
}
