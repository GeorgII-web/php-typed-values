<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias;

use PhpTypedValues\String\StringUrl;

/**
 * Alias for absolute URL string.
 *
 * Provides the same behavior as StringUrl while exposing a concise
 * name suitable for APIs that prefer "Url".
 *
 * Example
 *  - $u = Url::fromString('https://example.com/path?x=1');
 *    (string) $u; // "https://example.com/path?x=1"
 *
 * @method        false        isUndefined()
 * @method        string       value()
 * @method        bool         isEmpty()
 * @method        string       toString()
 * @method static static|mixed tryFromString(string $value, mixed $default = null)
 * @method static static|mixed tryFromMixed(mixed $value, mixed $default = null)
 *
 * @psalm-immutable
 */
final readonly class Url extends StringUrl
{
}
