<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Shared;

/**
 * Base contract to convert an object to array.
 *
 * @psalm-immutable
 */
interface ToArrayInterface
{
    public function toArray(): array;
}
