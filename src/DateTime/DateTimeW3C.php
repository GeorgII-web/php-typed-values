<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime;

use const DATE_W3C;

use DateTimeImmutable;
use DateTimeZone;
use PhpTypedValues\Abstract\DateTime\DateTimeType;
use PhpTypedValues\Exception\DateTimeTypeException;

/**
 * W3C RFC 3339 format based on ISO 8601.
 *
 * Example "2025-01-02T03:04:05+00:00"
 *
 * @psalm-immutable
 */
readonly class DateTimeW3C extends DateTimeType
{
    protected const FORMAT = DATE_W3C;

    /**
     * @throws DateTimeTypeException
     */
    public static function fromString(string $value): static
    {
        return new static(
            self::createFromFormat(
                $value,
                static::FORMAT,
                new DateTimeZone(static::ZONE)
            )
        );
    }

    public function toString(): string
    {
        return $this->value()->format(static::FORMAT);
    }

    public static function fromDateTime(DateTimeImmutable $value): static
    {
        return new static($value);
    }
}
