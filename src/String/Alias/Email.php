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
 * @psalm-immutable
 */
final class Email extends StringEmail
{
}
