<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Specific;

use PhpTypedValues\String\Specific\StringLocaleCode;

/**
 * Alias for Locale code string (e.g., en_US, de_DE).
 *
 * Provides the same behavior as StringLocaleCode while exposing a concise
 * name suitable for APIs that prefer "LocaleCode".
 *
 * Example
 *  - $l = LocaleCode::fromString('en_US');
 *    $l->toString(); // "en_US"
 *
 * @psalm-immutable
 */
final class LocaleCode extends StringLocaleCode
{
}
