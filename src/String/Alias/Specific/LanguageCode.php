<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Specific;

use PhpTypedValues\String\Specific\StringLanguageCode;

/**
 * Alias for ISO 639-1 two‑letter language code string.
 *
 * Provides the same behavior as StringLanguageCode while exposing a concise
 * name suitable for APIs that prefer "LanguageCode".
 *
 * Example
 *  - $l = LanguageCode::fromString('en');
 *    $l->toString(); // "en"
 *
 * @psalm-immutable
 */
final class LanguageCode extends StringLanguageCode
{
}
