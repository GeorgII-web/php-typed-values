<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime;

use const DATE_W3C;

use DateTimeImmutable;
use DateTimeZone;
use PhpTypedValues\Base\Primitive\DateTime\DateTimeType;
use PhpTypedValues\Exception\DateTimeTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Date-time value formatted using PHP's DATE_W3C (W3C profile of ISO 8601/RFC 3339).
 *
 * Uses the shared DateTimeType mechanics for strict parsing with aggregated
 * errors and warnings, exact round-trip verification, and normalized timezone
 * handling. Guarantees output using DATE_W3C format.
 *
 * Example
 *  - $v = DateTimeW3C::fromString('2025-01-02T03:04:05+00:00');
 *    $v->toString(); // "2025-01-02T03:04:05+00:00"
 *  - $v = DateTimeW3C::fromDateTime(new DateTimeImmutable('2030-12-31T23:59:59+03:00'));
 *    (string) $v; // "2030-12-31T23:59:59+03:00"
 *
 * @psalm-immutable
 */
readonly class DateTimeW3C extends DateTimeType
{
    protected const FORMAT = DATE_W3C;

    protected DateTimeImmutable $value;

    public function __construct(DateTimeImmutable $value)
    {
        $this->value = $value;
    }

    /**
     * @param non-empty-string $timezone
     */
    public static function tryFromMixed(mixed $value, string $timezone = self::ZONE): static|Undefined
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
    public static function fromString(string $value, string $timezone = self::ZONE): static
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
    public static function tryFromString(string $value, string $timezone = self::ZONE): static|Undefined
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
        // normalized timezone
        return new static(
            $value->setTimezone(new DateTimeZone(static::ZONE))
        );
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
