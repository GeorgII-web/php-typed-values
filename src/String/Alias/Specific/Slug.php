<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Specific;

use PhpTypedValues\String\Specific\StringSlug;

/**
 * Alias for URL slug string.
 *
 * Provides the same behavior as StringSlug while exposing a concise
 * name suitable for APIs that prefer "Slug".
 *
 * Example
 *  - $s = Slug::fromString('my-awesome-slug');
 *    $s->toString(); // "my-awesome-slug"
 *
 * @psalm-immutable
 */
final readonly class Slug extends StringSlug
{
}
