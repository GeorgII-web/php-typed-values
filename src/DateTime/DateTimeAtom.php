<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime;

use const DATE_ATOM;

use DateTimeImmutable;
use DateTimeZone;
use PhpTypedValues\Abstract\DateTime\DateTimeType;
use PhpTypedValues\Exception\DateTimeTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

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
     * @throws DateTimeTypeException
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static(
            static::createFromFormat(
                $value,
                static::FORMAT,
                new DateTimeZone(static::ZONE)
            )
        );
    }

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value)
    {
        try {
            return static::fromString($value);
        } catch (TypeException $exception) {
            return Undefined::create();
        }
    }

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

    public function jsonSerialize(): string
    {
        return $this->toString();
    }
}
