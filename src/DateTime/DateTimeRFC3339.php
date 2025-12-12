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
class DateTimeRFC3339 extends DateTimeType
{
    protected const FORMAT = DATE_RFC3339;

    /**
     * @readonly
     */
    protected DateTimeImmutable $value;

    public function __construct(DateTimeImmutable $value)
    {
        $this->value = $value;
    }

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     * @param mixed $value
     */
    public static function tryFromMixed($value)
    {
        try {
            return static::fromString(
                static::convertMixedToString($value)
            );
        } catch (TypeException $exception) {
            return Undefined::create();
        }
    }

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
}
