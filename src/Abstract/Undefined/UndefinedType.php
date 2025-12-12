<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Undefined;

use PhpTypedValues\Abstract\AbstractType;
use PhpTypedValues\Exception\UndefinedTypeException;

/**
 * Base implementation for a special "Undefined/Unknown" typed value.
 *
 * Use it in APIs that must return a typed value when no meaningful value is available yet.
 * Prefer this over null to make intent explicit and keep type-safety.
 *
 * Example
 *  - return Undefined::create();
 *  - $v->toString(); // throws UndefinedTypeException
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract class UndefinedType extends AbstractType implements UndefinedTypeInterface
{
}
