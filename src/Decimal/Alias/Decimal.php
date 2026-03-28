<?php

declare(strict_types=1);

namespace PhpTypedValues\Decimal\Alias;

use PhpTypedValues\Decimal\DecimalStandard;

/**
 * Alias for MariaDB DECIMAL value represented as a string.
 *
 * Provides the same behavior as StringDecimal while exposing a concise
 * name suitable for APIs that prefer "Decimal" in the MariaDb namespace.
 *
 * Example
 *  - $d = Decimal::fromString('3.14');
 *    $d->toFloat(); // 3.14 (only if exact)
 *
 * @psalm-immutable
 */
final class Decimal extends DecimalStandard
{
}
