<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\MariaDb;

use PhpTypedValues\String\MariaDb\StringText;

/**
 * Alias for MariaDB TEXT string (up to 65,535 characters).
 *
 * Provides the same behavior as StringText while exposing a concise
 * name suitable for APIs that prefer "Text" in the MariaDb namespace.
 *
 * Example
 *  - $t = Text::fromString('lorem ipsum');
 *    $t->toString(); // 'lorem ipsum'
 *
 * @psalm-immutable
 */
final readonly class Text extends StringText
{
}
