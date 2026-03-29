<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Specific;

use PhpTypedValues\String\Specific\StringSemVer;

/**
 * Alias for Semantic Versioning string.
 *
 * Provides the same behavior as StringSemVer while exposing a concise
 * name suitable for APIs that prefer "SemVer".
 *
 * Example
 *  - $v = SemVer::fromString('1.2.3');
 *    $v->toString(); // "1.2.3"
 *
 * @psalm-immutable
 */
final class SemVer extends StringSemVer
{
}
