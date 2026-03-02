<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Specific;

use PhpTypedValues\String\Specific\StringIpV4;

/**
 * Alias for IPv4 address string.
 *
 * Provides the same behavior as StringIpV4 while exposing a concise
 * name suitable for APIs that prefer "IpV4".
 *
 * Example
 *  - $v = IpV4::fromString('127.0.0.1');
 *    $v->toString(); // "127.0.0.1"
 *
 * @psalm-immutable
 */
final class IpV4 extends StringIpV4
{
}
