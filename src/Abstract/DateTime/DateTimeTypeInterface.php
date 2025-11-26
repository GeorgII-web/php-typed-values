<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\DateTime;

use DateTimeImmutable;

/**
 * @psalm-immutable
 */
interface DateTimeTypeInterface
{
    public function value(): DateTimeImmutable;

    public static function fromDateTime(DateTimeImmutable $value): self;

    public function toString(): string;

    public static function fromString(string $value): self;
}
