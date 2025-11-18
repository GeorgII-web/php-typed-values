<?php

declare(strict_types=1);

namespace GeorgiiWeb\PhpTypedValues\Types\Integer;

use GeorgiiWeb\PhpTypedValues\Types\Base\BaseIntType;

/**
 * @psalm-immutable
 */
final class Integer extends BaseIntType
{
    public function assert(int $value): void
    {
        // Do nothing, $value already int
    }
}
