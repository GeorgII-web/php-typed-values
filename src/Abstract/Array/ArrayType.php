<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Array;

use IteratorAggregate;
use JsonSerializable;

/**
 * Base implementation for array typed values.
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
abstract class ArrayType implements ArrayTypeInterface, IteratorAggregate, JsonSerializable
{
}
