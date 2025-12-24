<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias;

use PhpTypedValues\String\StringPath;

/**
 * Alias for path string.
 *
 * Validates that the string is a valid path.
 *
 * Example
 *  - $p = StringPath::fromString('/src/String');
 *  - $p = StringPath::fromString('src\String\');
 *
 * @method        string       value()
 * @method static static|mixed tryFromString(string $value, mixed $default = null)
 *
 * @psalm-immutable
 */
final readonly class Path extends StringPath
{
}
