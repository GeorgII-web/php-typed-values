<?php

declare(strict_types=1);

namespace PhpTypedValues\Undefined\Alias;

use PhpTypedValues\Undefined\UndefinedStandard;

/**
 * Implementation for a special "NotFound" typed value.
 *
 * Use it in APIs that must return a typed value when no meaningful value is available yet.
 * Prefer this over null to make intent explicit and keep type-safety.
 *
 * Example
 *  - return NotFound::create();
 *  - $v->toString(); // throws UndefinedTypeException
 *
 * @psalm-immutable
 */
final class NotFound extends UndefinedStandard
{
}
