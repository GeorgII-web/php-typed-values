<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias;

use PhpTypedValues\String\StringFileName;

/**
 * Alias for a file name string.
 *
 * Validates that the string is a valid file name (not a path).
 * Rejects path separators and characters that are typically invalid in file names
 * across major operating systems.
 *
 * Example
 *  - $f = StringFileName::fromString('image.jpg');
 *    $f->getFileNameOnly(); // "image"
 *    $f->getExtension(); // "jpg"
 *
 * @method        false            isUndefined()
 * @method        non-empty-string value()
 * @method        bool             isEmpty()
 * @method        string           toString()
 * @method static static|mixed     tryFromString(string $value, mixed $default = null)
 * @method static static|mixed     tryFromMixed(mixed $value, mixed $default = null)
 *
 * @psalm-immutable
 */
final readonly class File extends StringFileName
{
}
