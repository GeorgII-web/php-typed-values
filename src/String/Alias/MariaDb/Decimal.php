<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\MariaDb;

use PhpTypedValues\String\MariaDb\StringDecimal;

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
 * @method        false        isUndefined()
 * @method        string       value()
 * @method        bool         isEmpty()
 * @method        string       toString()
 * @method static static|mixed tryFromString(string $value, mixed $default = null)
 * @method static static|mixed tryFromMixed(mixed $value, mixed $default = null)
 *
 * @psalm-immutable
 */
final readonly class Decimal extends StringDecimal
{
}
