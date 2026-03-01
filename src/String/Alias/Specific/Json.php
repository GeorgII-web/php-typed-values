<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Specific;

use PhpTypedValues\String\Specific\StringJson;

/**
 * Alias for JSON text string.
 *
 * Provides the same behavior as StringJson while exposing a concise
 * name suitable for APIs that prefer "Json".
 *
 * Example
 *  - $j = Json::fromString('{"a":1}');
 *    $j->toArray(); // ['a' => 1]
 *
 * @psalm-immutable
 */
final class Json extends StringJson
{
}
