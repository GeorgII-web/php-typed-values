<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Array;

use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Contract for array typed values.
 *
 * Represents a read‑only collection of typed items with factory helpers
 * to construct the collection from raw arrays. Implementations are
 * immutable and iterable.
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @template TItem
 *
 * @psalm-immutable
 */
interface ArrayTypeInterface
{
    /**
     * Returns the underlying typed items.
     *
     * @psalm-return list<TItem>
     */
    public function value(): array;

    /**
     * Creates a new collection from a list of raw values.
     * Implementations MUST fail early on invalid input.
     *
     * @param array $value raw input values
     *
     * @psalm-param list<mixed> $value
     * @return static
     */
    public static function fromArray(array $value);

    /**
     * Creates a new collection from a list of raw values, allowing
     * late/optional failure semantics via `Undefined` where applicable.
     *
     * @param array $value raw input values
     *
     * @psalm-param list<mixed> $value
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromArray(array $value);
}
