<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\MariaDb;

use PhpTypedValues\String\MariaDb\StringLongText;

/**
 * Alias for MariaDB LONGTEXT string (up to 4,294,967,295 characters).
 *
 * Provides the same behavior as StringLongText while exposing a concise
 * name suitable for APIs that prefer "LongText".
 *
 * Example
 *  - $t = LongText::fromString('lorem ipsum');
 *    $t->toString(); // 'lorem ipsum'
 *
 * @psalm-immutable
 */
final class LongText extends StringLongText
{
}
