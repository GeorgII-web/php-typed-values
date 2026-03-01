<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Specific;

use PhpTypedValues\String\Specific\StringIpV6;

/**
 * Alias for IPv6 address string.
 *
 * Provides the same behavior as StringIpV6 while exposing a concise
 * name suitable for APIs that prefer "IpV6".
 *
 * Example
 *  - $v = IpV6::fromString('::1');
 *    $v->toString(); // "::1"
 *
 * @psalm-immutable
 */
final readonly class IpV6 extends StringIpV6
{
}
