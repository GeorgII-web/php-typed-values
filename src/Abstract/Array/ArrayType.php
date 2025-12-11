<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Array;

use IteratorAggregate;
use JsonSerializable;

/**
 * Base implementation for array typed values.
 *
 * Provides an immutable, iterable, and JSON‑serializable collection of
 * typed items. Concrete implementations define item validation and
 * factory behavior.
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @template TItem
 *
 * @implements IteratorAggregate<int, TItem>
 *
 * @template-implements ArrayTypeInterface<TItem>
 *
 * @psalm-immutable
 */
abstract readonly class ArrayType implements ArrayTypeInterface, IteratorAggregate, JsonSerializable
{
}
