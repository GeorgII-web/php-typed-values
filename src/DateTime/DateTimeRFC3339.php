<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime;

use const DATE_RFC3339;

use DateTimeImmutable;
use DateTimeZone;
use PhpTypedValues\Abstract\DateTime\DateTimeType;
use PhpTypedValues\Exception\DateTimeTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * RFC 3339 format based on ISO 8601.
 *
 * Example "2025-01-02T03:04:05+00:00"
 *
 * @psalm-immutable
 */
readonly class DateTimeRFC3339 extends DateTimeType
{
    protected const FORMAT = DATE_RFC3339;

    /**
     * @throws DateTimeTypeException
     */
    public static function fromString(string $value): static
    {
        return new static(
            static::createFromFormat(
                $value,
                static::FORMAT,
                new DateTimeZone(static::ZONE)
            )
        );
    }

    public static function tryFromString(string $value): static|Undefined
    {
        try {
            return static::fromString($value);
        } catch (TypeException) {
            return Undefined::create();
        }
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
