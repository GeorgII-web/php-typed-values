<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Uuid;

use PhpTypedValues\String\Uuid\StringUuidV7;

/**
 * Alias for UUID version 7 (time‑ordered) string.
 *
 * Provides the same behavior as StringUuidV7 while exposing a concise
 * name suitable for APIs that prefer "UuidV7".
 *
 * Example
 *  - $u = UuidV7::fromString('01890F2A-5BCD-7DEF-9ABC-1234567890AB');
 *    $u->toString(); // '01890f2a-5bcd-7def-9abc-1234567890ab'
 *
 * @method        false            isUndefined()
 * @method        non-empty-string value()
 * @method        bool             isEmpty()
 * @method static static|mixed     tryFromString(string $value, mixed $default = null)
 * @method static static|mixed     tryFromMixed(mixed $value, mixed $default = null)
 *
 * @psalm-immutable
 */
final readonly class UuidV7 extends StringUuidV7
{
}
