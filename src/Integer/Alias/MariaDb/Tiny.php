<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\Alias\MariaDb;

use PhpTypedValues\Integer\MariaDb\IntegerTiny;

/**
 * Tiny (Alias for MariaDB signed TINYINT).
 *
 * Provides the same behavior as IntegerTiny while exposing a concise name
 * suitable for APIs that prefer "Tiny" in the MariaDB namespace.
 *
 * Example
 *  - $v = Tiny::fromInt(1);
 *    $v->value(); // 1
 *  - $v = Tiny::fromString('-5');
 *    (string) $v; // "-5"
 *
 * @psalm-immutable
 */
final class Tiny extends IntegerTiny
{
}
