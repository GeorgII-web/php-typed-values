<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\MariaDb;

use PhpTypedValues\String\MariaDb\StringVarChar255;

/**
 * Alias for MariaDB VARCHAR(255) string.
 *
 * Provides the same behavior as StringVarChar255 while exposing a concise
 * name suitable for APIs that prefer "VarChar255" in the MariaDb namespace.
 *
 * Example
 *  - $v = VarChar255::fromString('Hello world');
 *    $v->toString(); // 'Hello world'
 *
 * @method        false        isUndefined()
 * @method        string       value()
 * @method        bool         isEmpty()
 * @method static static|mixed tryFromString(string $value, mixed $default = null)
 * @method static static|mixed tryFromMixed(mixed $value, mixed $default = null)
 *
 * @psalm-immutable
 */
final readonly class VarChar255 extends StringVarChar255
{
}
