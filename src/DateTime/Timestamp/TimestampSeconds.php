<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime\Timestamp;

use DateTimeZone;
use PhpTypedValues\Abstract\DateTime\DateTimeType;
use PhpTypedValues\Exception\DateTimeTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Unix timestamp value in whole seconds since the Unix epoch (UTC).
 *
 * Parses and formats using the 'U' pattern via the base DateTimeType, which
 * performs strict error aggregation, round‑trip validation, and timezone
 * normalization. Output has been guaranteed to be seconds since epoch as a string.
 *
 * Example
 *  - $v = TimestampSeconds::fromString('1732445696');
 *    $v->toString(); // "1732445696"
 *  - (string) TimestampSeconds::fromString('0'); // "0"
 *
 * @psalm-immutable
 */
class TimestampSeconds extends DateTimeType
{
    /**
     * DateTime::format() pattern for Unix timestamp.
     *
     * @see https://www.php.net/manual/en/datetime.format.php
     */
    protected const FORMAT = 'U';

    /**
     * Parse from a numeric Unix timestamp string (seconds).
     *
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

    public function jsonSerialize(): int
    {
        return (int) $this->toString();
    }
}
