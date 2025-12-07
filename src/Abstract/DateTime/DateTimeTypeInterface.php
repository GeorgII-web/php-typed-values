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

    /**
     * @return static
     */
    public static function fromDateTime(DateTimeImmutable $value);

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value);

    public function toString(): string;

    /**
     * @return static
     */
    public static function fromString(string $value);

    public function __toString(): string;
}
