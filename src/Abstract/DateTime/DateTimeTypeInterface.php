<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\DateTime;

use DateTimeImmutable;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
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
