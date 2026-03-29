<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Specific;

use PhpTypedValues\String\Specific\StringBase64;

/**
 * Alias for Base64-encoded string.
 *
 * Provides the same behavior as StringBase64 while exposing a concise
 * name suitable for APIs that prefer "Base64".
 *
 * Example
 *  - $b = Base64::fromString('SGVsbG8gV29ybGQ=');
 *    $b->toString(); // "SGVsbG8gV29ybGQ="
 *
 * @psalm-immutable
 */
final class Base64 extends StringBase64
{
}
