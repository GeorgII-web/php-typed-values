<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Specific;

use PhpTypedValues\String\Specific\StringCurrencyCode;

/**
 * Alias for ISO 4217 three‑letter currency code string.
 *
 * Provides the same behavior as StringCurrencyCode while exposing a concise
 * name suitable for APIs that prefer "CurrencyCode".
 *
 * Example
 *  - $c = CurrencyCode::fromString('USD');
 *    $c->toString(); // "USD"
 *
 * @psalm-immutable
 */
final readonly class CurrencyCode extends StringCurrencyCode
{
}
