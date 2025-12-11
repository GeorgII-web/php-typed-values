<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Array;

use IteratorAggregate;
use JsonSerializable;
use PhpTypedValues\Abstract\AbstractType;

/**
 * Base implementation for array typed values.
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract readonly class ArrayType implements ArrayTypeInterface, IteratorAggregate, JsonSerializable
{
}
