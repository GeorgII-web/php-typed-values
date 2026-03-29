<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\MariaDb;

use PhpTypedValues\String\MariaDb\StringTinyText;

/**
 * Alias for MariaDB TINYTEXT string.
 *
 * Provides the same behavior as StringTinyText while exposing a concise
 * name suitable for APIs that prefer "TinyText".
 *
 * Example
 *  - $v = TinyText::fromString('Hello world');
 *    $v->toString(); // 'Hello world'
 *
 * @psalm-immutable
 */
final class TinyText extends StringTinyText
{
}
