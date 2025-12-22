<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Shared;

/**
 * Base contract to create an object from an array.
 *
 * @psalm-immutable
 */
interface FromArray
{
    public static function fromArray(array $value): static;
}
