<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime;

use const DATE_RFC3339_EXTENDED;

use DateTimeImmutable;
use DateTimeZone;
use PhpTypedValues\Base\Primitive\DateTime\DateTimeType;
use PhpTypedValues\Exception\DateTimeTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Date-time value formatted using PHP's DATE_RFC3339_EXTENDED (RFC 3339 with microseconds).
 *
 * Inherits strict parsing and validation from DateTimeType, including detailed
 * parser error aggregation and exact round-trip checks. Ensures output
 * formatting with DATE_RFC3339_EXTENDED, preserving fractional seconds and
 * timezone offset.
 *
 * Example
 *  - $v = DateTimeRFC3339Extended::fromString('2025-01-02T03:04:05.123456+00:00');
 *    $v->toString(); // "2025-01-02T03:04:05.123456+00:00"
 *  - $v = DateTimeRFC3339Extended::fromDateTime(new DateTimeImmutable('2030-12-31T23:59:59.654321+03:00'));
 *    (string) $v; // "2030-12-31T23:59:59.654321+03:00"
 *
 * @psalm-immutable
 */
readonly class DateTimeRFC3339Extended extends DateTimeType
{
    protected const FORMAT = DATE_RFC3339_EXTENDED;

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
