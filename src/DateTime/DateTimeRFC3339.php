<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime;

use const DATE_RFC3339;

use DateTimeImmutable;
use DateTimeZone;
use PhpTypedValues\Base\Primitive\DateTime\DateTimeType;
use PhpTypedValues\Exception\DateTimeTypeException;

/**
 * Date-time value formatted using PHP's DATE_RFC3339 (RFC 3339 based on ISO 8601).
 *
 * Leverages the common DateTimeType parsing that aggregates parser errors and
 * warnings, enforces strict round-trip formatting, and normalizes timezone
 * handling. Guarantee output formatted with DATE_RFC3339.
 *
 * Example
 *  - $v = DateTimeRFC3339::fromString('2025-01-02T03:04:05+00:00');
 *    $v->toString(); // "2025-01-02T03:04:05+00:00"
 *  - $v = DateTimeRFC3339::fromDateTime(new DateTimeImmutable('2030-12-31T23:59:59+03:00'));
 *    (string) $v; // "2030-12-31T23:59:59+03:00"
 *
 * @psalm-immutable
 */
readonly class DateTimeRFC3339 extends DateTimeType
{
    protected const FORMAT = DATE_RFC3339;

    protected DateTimeImmutable $value;

    public function __construct(DateTimeImmutable $value)
    {
        // normalized time zone
        $this->value = $value->setTimezone(new DateTimeZone(static::DEFAULT_ZONE));
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
