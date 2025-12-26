<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime\MariaDb;

use DateTimeImmutable;
use DateTimeZone;
use PhpTypedValues\Base\Primitive\DateTime\DateTimeType;
use PhpTypedValues\Exception\DateTimeTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Date-time value formatted using 'Y-m-d H:i:s' (SQL\Human format).
 *
 * Provides strict parsing with detailed error aggregation via the base
 * DateTimeType, preserves the exact offset, and guarantees round-trip
 * formatting using SQL format.
 *
 * Example
 *  - $v = DateTimeSql::fromString('2025-01-02 03:04:05');
 *    $v->toString(); // "2025-01-02 03:04:05"
 *  - $v = DateTimeSql::fromDateTime(new DateTimeImmutable('2030-12-31 23:59:59'));
 *    (string) $v; // "2030-12-31 23:59:59"
 *
 * @psalm-immutable
 */
readonly class DateTimeSql extends DateTimeType
{
    protected const FORMAT = 'Y-m-d H:i:s';

    protected DateTimeImmutable $value;

    public function __construct(DateTimeImmutable $value)
    {
        // normalized time zone
        $this->value = $value->setTimezone(new DateTimeZone(static::DEFAULT_ZONE));
    }

    /**
     * @param non-empty-string $timezone
     */
    public static function tryFromMixed(mixed $value, string $timezone = self::DEFAULT_ZONE): static|Undefined
    {
        try {
            return static::fromString(
                static::convertMixedToString($value),
                $timezone
            );
        } catch (TypeException) {
            return Undefined::create();
        }
    }

    /**
     * @param non-empty-string $timezone
     *
     * @throws DateTimeTypeException
     */
    public static function fromString(string $value, string $timezone = self::DEFAULT_ZONE): static
    {
        return new static(
            static::createFromFormat(
                $value,
                static::FORMAT,
                new DateTimeZone($timezone)
            )
        );
    }

    /**
     * @param non-empty-string $timezone
     */
    public static function tryFromString(string $value, string $timezone = self::DEFAULT_ZONE): static|Undefined
    {
        try {
            return static::fromString($value, $timezone);
        } catch (TypeException) {
            return Undefined::create();
        }
    }

    public function withTimeZone(string $timezone): static
    {
        return new static(
            $this->value()->setTimezone(new DateTimeZone($timezone))
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

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function value(): DateTimeImmutable
    {
        return $this->value;
    }

    public static function getFormat(): string
    {
        return static::FORMAT;
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }
}
