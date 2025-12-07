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

    /**
     * @return static
     */
    public static function fromDateTime(DateTimeImmutable $value);

    public function toString(): string;

    /**
     * @return static
     */
    public static function fromString(string $value);

    public function __toString(): string;
}
