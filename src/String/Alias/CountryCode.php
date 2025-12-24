<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias;

use PhpTypedValues\String\StringCountryCode;

/**
 * Alias for ISO 3166-1 alpha‑2 country code string.
 *
 * Provides the same behavior as StringCountryCode while exposing a concise
 * name suitable for APIs that prefer "CountryCode".
 *
 * Example
 *  - $c = CountryCode::fromString('US');
 *    $c->toString(); // "US"
 *
 * @method        string       value()
 * @method static static|mixed tryFromString(string $value, mixed $default = null)
 *
 * @psalm-immutable
 */
final readonly class CountryCode extends StringCountryCode
{
}
