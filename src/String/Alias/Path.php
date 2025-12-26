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
 * @method        false        isUndefined()
 * @method        string       value()
 * @method        bool         isEmpty()
 * @method static static|mixed tryFromString(string $value, mixed $default = null)
 * @method static static|mixed tryFromMixed(mixed $value, mixed $default = null)
 *
 * @psalm-immutable
 */
final readonly class Path extends StringPath
{
}
