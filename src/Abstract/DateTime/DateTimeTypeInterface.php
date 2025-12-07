<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\DateTime;

use DateTimeImmutable;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Contract for DateTime typed values.
 *
 * Declares the API for value objects backed by DateTimeImmutable, including
 * factories from strings and native DateTime, string formatting, and
 * a safe try-from factory returning Undefined on invalid input.
 *
 * Example
 *  - $v = MyDateTime::fromDateTime(new DateTimeImmutable('2025-01-02T03:04:05+00:00'));
 *  - $v->toString(); // '2025-01-02T03:04:05+00:00'
 *
 * @psalm-immutable
 */
interface DateTimeTypeInterface
{
    public function value(): DateTimeImmutable;

    public static function fromDateTime(DateTimeImmutable $value): static;

    public static function tryFromString(string $value): static|Undefined;

    public function toString(): string;

    public static function fromString(string $value): static;

    public function __toString(): string;
}
