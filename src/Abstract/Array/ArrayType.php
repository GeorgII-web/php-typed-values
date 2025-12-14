<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Array;

/**
 * Base implementation for array typed values.
 *
 * Provides an immutable, iterable, countable, and JSON‑serializable
 * collection of typed items. Concrete implementations define item
 * validation and factory behavior.
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @template TItem
 *
 * @template-implements ArrayTypeInterface<TItem>
 *
 * @psalm-immutable
 */
abstract class ArrayType implements ArrayTypeInterface
{
}
