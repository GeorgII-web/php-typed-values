<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias;

use PhpTypedValues\String\Specific\StringJwt;

/**
 * Alias for the JWT string typed value.
 *
 * @psalm-immutable
 */
final readonly class Jwt extends StringJwt
{
}
