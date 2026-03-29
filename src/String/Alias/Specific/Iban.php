<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Specific;

use PhpTypedValues\String\Specific\StringIban;

/**
 * Alias for IBAN string.
 *
 * Provides the same behavior as StringIban while exposing a concise
 * name suitable for APIs that prefer "Iban".
 *
 * Example
 *  - $i = Iban::fromString('DE89 3704 0044 0532 0130 00');
 *    $i->toString(); // "DE89370400440532013000"
 *
 * @psalm-immutable
 */
final class Iban extends StringIban
{
}
