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
class DateTimeRFC3339 extends DateTimeType
{
    protected const FORMAT = DATE_RFC3339;

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
}
