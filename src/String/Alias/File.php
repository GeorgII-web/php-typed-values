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
 * @method        non-empty-string value()
 * @method static static|mixed     tryFromString(string $value, mixed $default = null)
 *
 * @psalm-immutable
 */
final readonly class File extends StringFileName
{
}
