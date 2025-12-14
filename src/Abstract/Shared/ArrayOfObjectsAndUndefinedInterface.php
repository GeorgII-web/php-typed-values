<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Shared;

/**
 * Base contract for Emptiness check for any typed value object.
 *
 * @template TItem
 *
 * @psalm-immutable
 */
interface ArrayOfObjectsAndUndefinedInterface
{
    /**
     * Returns true if at least one item in the collection is Undefined.
     */
    public function hasUndefined(): bool;

    /**
     * Returns items excluding Undefined entries.
     *
     * @psalm-return list<TItem>
     */
    public function getDefinedItems(): array;
}
