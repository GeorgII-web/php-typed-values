<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Specific;

use PhpTypedValues\String\Specific\StringMacAddress;

/**
 * Alias for MAC address string.
 *
 * Provides the same behavior as StringMacAddress while exposing a concise
 * name suitable for APIs that prefer "MacAddress".
 *
 * Example
 *  - $v = MacAddress::fromString('00:00:5e:00:53:01');
 *    $v->toString(); // "00:00:5e:00:53:01"
 *
 * @psalm-immutable
 */
final readonly class MacAddress extends StringMacAddress
{
}
