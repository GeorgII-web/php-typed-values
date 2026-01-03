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
 * @psalm-immutable
 */
final class File extends StringFileName
{
}
