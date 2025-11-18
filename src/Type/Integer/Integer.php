<?php

declare(strict_types=1);

namespace PhpTypedValues\Type\Integer;

use Override;
use PhpTypedValues\BaseType\BaseIntType;

/**
 * @psalm-immutable
 */
final readonly class Integer extends BaseIntType
{
    #[Override]
    public function assert(int $value): void
    {
        // Do nothing, $value already int
    }
}
