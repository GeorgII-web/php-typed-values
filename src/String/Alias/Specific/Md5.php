<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Specific;

use PhpTypedValues\String\Specific\StringMd5;

/**
 * Alias for MD5 hash string.
 *
 * Provides the same behavior as StringMd5 while exposing a concise
 * name suitable for APIs that prefer "Md5".
 *
 * Example
 *  - $m = Md5::fromString('5d41402abc4b2a76b9719d911017c592');
 *    $m->toString(); // "5d41402abc4b2a76b9719d911017c592"
 *
 * @psalm-immutable
 */
final readonly class Md5 extends StringMd5
{
}
