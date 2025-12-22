<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Bool;

use PhpTypedValues\Base\Primitive\PrimitiveType;

/**
 * Base implementation for boolean typed values.
 *
 * Provides common formatting helpers and factory methods for bool-backed
 * value objects. Concrete boolean types extend this class and add
 * domain-specific validation if needed.
 *
 * Example
 *  - $v = MyBoolean::fromBool(true);
 *  - $v->toString(); // "true"
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract class BoolType extends PrimitiveType implements BoolTypeInterface
{
}
