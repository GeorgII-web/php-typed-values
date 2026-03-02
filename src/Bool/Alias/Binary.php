<?php

declare(strict_types=1);

namespace PhpTypedValues\Bool\Alias;

use PhpTypedValues\Bool\BoolStandard;

/**
 * Alias for the generic boolean typed value.
 *
 * Provides the same behavior as BoolStandard while offering a more
 * descriptive name for APIs that prefer "Boolean".
 *
 * Example
 *  - $b = Binary::fromString('true');
 *    $b->toString(); // "true"
 *
 * @psalm-immutable
 */
final class Binary extends BoolStandard
{
}
