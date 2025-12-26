<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias;

use PhpTypedValues\String\StringEmail;

/**
 * Alias for email address string.
 *
 * Provides the same behavior as StringEmail while exposing a concise
 * name suitable for APIs that prefer "Email".
 *
 * Example
 *  - $e = Email::fromString('user@example.com');
 *    (string) $e; // "user@example.com"
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
final readonly class Email extends StringEmail
{
}
