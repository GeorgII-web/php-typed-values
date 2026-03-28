<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\MariaDb;

use PhpTypedValues\String\MariaDb\StringMediumText;

/**
 * Alias for MariaDB MEDIUMTEXT string (up to 16,777,215 characters).
 *
 * Provides the same behavior as StringMediumText while exposing a concise
 * name suitable for APIs that prefer "MediumText".
 *
 * Example
 *  - $t = MediumText::fromString('lorem ipsum');
 *    $t->toString(); // 'lorem ipsum'
 *
 * @psalm-immutable
 */
final class MediumText extends StringMediumText
{
}
