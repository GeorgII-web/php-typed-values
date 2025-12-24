<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias;

use PhpTypedValues\String\StringJson;

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
final readonly class Json extends StringJson
{
}
