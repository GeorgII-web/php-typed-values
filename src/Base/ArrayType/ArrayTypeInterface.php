<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\ArrayType;

use Countable;
use IteratorAggregate;
use JsonSerializable;
use PhpTypedValues\ArrayType\ArrayUndefinedAbstract;
use PhpTypedValues\Base\Shared\IsEmptyInterface;
use PhpTypedValues\Base\Shared\IsUndefinedInterface;
use PhpTypedValues\Base\TypeInterface;
use PhpTypedValues\Exception\Array\ArrayTypeException;

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
 *
 * @psalm-immutable
 */
interface ArrayTypeInterface extends TypeInterface, JsonSerializable, IteratorAggregate, Countable, IsEmptyInterface, IsUndefinedInterface
{
    /**
     * Creates a new collection from a list of Objects.
     * Implementations MUST fail early on invalid input.
     *
     * @param list<mixed> $value
     *
     * @throws ArrayTypeException
     * @return static
     */
    public static function fromArray(array $value);

    /**
     * @template TSelf of ArrayTypeInterface
     *
     * Returns items excluding Undefined entries.
     *
     * @phpstan-return list<TItem>
     *
     * @psalm-return (TSelf is ArrayUndefinedAbstract ? never : list<TItem>)
     *
     * @psalm-suppress PossiblyUnusedReturnValue
     */
    public function getDefinedItems(): array;

    /**
     * Returns true if at least one item in the collection is Undefined.
     */
    public function hasUndefined(): bool;

    /**
     * Convert to an array of scalars from an array of Objects.
     * Each Item should implement JsonSerializable interface.
     *
     * @throws ArrayTypeException
     *
     * @psalm-mutation-free
     */
    public function toArray(): array;

    /**
     * Creates a new collection from a list of Objects or scalars (which
     * will be converted to Undefined type class), allowing late/optional
     * failure semantics via `Undefined` where applicable.
     *
     * @template T of ArrayTypeInterface
     *
     * @param list<mixed> $value
     * @param T           $default
     *
     * @return static|T
     *
     * @psalm-return ($default is ArrayUndefinedAbstract ? static : static|T)
     */
    public static function tryFromArray(
        array $value,
        self $default = null
    );

    /**
     * Returns the underlying Objects array.
     *
     * @psalm-return list<TItem>
     */
    public function value(): array;
}
