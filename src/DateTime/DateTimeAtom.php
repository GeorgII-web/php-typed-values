<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime;

use const DATE_ATOM;

use DateTimeImmutable;
use DateTimeZone;
use PhpTypedValues\Base\Primitive\DateTime\DateTimeType;
use PhpTypedValues\Exception\DateTimeTypeException;
use PhpTypedValues\Exception\ReasonableRangeDateTimeTypeException;

/**
 * Date-time value formatted using PHP's DATE_ATOM (RFC 3339 based on ISO 8601).
 *
 * Provides strict parsing with detailed error aggregation via the base
 * DateTimeType, preserves the exact offset, and guarantees round-trip
 * formatting using DATE_ATOM.
 *
 * Example
 *  - $v = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');
 *    $v->toString(); // "2025-01-02T03:04:05+00:00"
 *  - $v = DateTimeAtom::fromDateTime(new DateTimeImmutable('2030-12-31T23:59:59+03:00'));
 *    (string) $v; // "2030-12-31T23:59:59+03:00"
 *
 * @psalm-immutable
 */
class DateTimeAtom extends DateTimeType
{
    protected const FORMAT = DATE_ATOM;

    /**
     * @readonly
     */
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
     * @return static
     */
    public static function fromString(string $value, string $timezone = self::DEFAULT_ZONE)
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
     * @throws ReasonableRangeDateTimeTypeException
     * @throws DateTimeTypeException
     * @return static
     */
    public function withTimeZone(string $timezone)
    {
        return new static(
            $this->value()->setTimezone(new DateTimeZone($timezone))
        );
    }

    /**
     * @throws ReasonableRangeDateTimeTypeException
     * @throws DateTimeTypeException
     */
    public function toString(): string
    {
        return $this->value()->format(static::FORMAT);
    }

    /**
     * @return static
     */
    public static function fromDateTime(DateTimeImmutable $value)
    {
        return new static($value);
    }

    /**
     * @throws ReasonableRangeDateTimeTypeException
     * @throws DateTimeTypeException
     */
    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * @throws ReasonableRangeDateTimeTypeException
     * @throws DateTimeTypeException
     */
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
