<?php

declare(strict_types=1);

namespace PhpTypedValues\Internal\Shared;

/**
 * Base contract for Emptiness check for any typed value object.
 *
 * @psalm-immutable
 */
interface IsEmptyInterface
{
    /**
     * Returns true if the Object value is empty.
     */
    public function isEmpty(): bool;
}
