<?php

declare(strict_types=1);

namespace PhpTypedValues\Internal\ArrayType;

use Countable;
use IteratorAggregate;
use JsonSerializable;
use PhpTypedValues\Exception\ArrayTypeException;
use PhpTypedValues\Internal\Shared\ArrayOfObjectsAndUndefinedInterface;
use PhpTypedValues\Internal\Shared\IsEmptyInterface;
use PhpTypedValues\Internal\Shared\IsUndefinedInterface;
use PhpTypedValues\Internal\TypeInterface;

/**
 * Contract for array typed values.
 *
 * Represents a read‑only collection of typed items with factory helpers
 * to construct the collection from raw arrays. Implementations are
 * immutable, iterable, countable, and serializable.
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @template TItem
 *
 * @extends IteratorAggregate<int, TItem>
 * @extends ArrayOfObjectsAndUndefinedInterface<TItem>
 *
 * @psalm-immutable
 */
interface ArrayTypeInterface extends TypeInterface, JsonSerializable, IteratorAggregate, Countable, IsEmptyInterface, IsUndefinedInterface, ArrayOfObjectsAndUndefinedInterface
{
    /**
     * Returns the underlying Objects array.
     *
     * @psalm-return list<TItem>
     */
    public function value(): array;

    /**
     * Creates a new collection from a list of Objects.
     * Implementations MUST fail early on invalid input.
     *
     * @param list<mixed> $value
     *
     * @throws ArrayTypeException
     */
    public static function fromArray(array $value): static;

    /**
     * Creates a new collection from a list of Objects or scalars (which
     * will be converted to Undefined type class), allowing late/optional
     * failure semantics via `Undefined` where applicable.
     *
     * @param list<mixed> $value
     *
     * @throws ArrayTypeException
     */
    public static function tryFromArray(array $value): static;

    /**
     * Convert to an array of scalars from an array of Objects.
     * Each Item should implement JsonSerializable interface.
     *
     * @throws ArrayTypeException
     *
     * @psalm-mutation-free
     */
    public function toArray(): array;
}
