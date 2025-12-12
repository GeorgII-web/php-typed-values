<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime\Timestamp;

use DateTimeImmutable;
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
readonly class TimestampSeconds extends DateTimeType
{
    /**
     * DateTime::format() pattern for Unix timestamp.
     *
     * @see https://www.php.net/manual/en/datetime.format.php
     */
    protected const FORMAT = 'U';

    protected DateTimeImmutable $value;

    public function __construct(DateTimeImmutable $value)
    {
        $this->value = $value;
    }

    public static function tryFromMixed(mixed $value): static|Undefined
    {
        try {
            return static::fromString(
                static::convertMixedToString($value)
            );
        } catch (TypeException) {
            return Undefined::create();
        }
    }

    /**
     * Parse from a numeric Unix timestamp string (seconds).
     *
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

    public static function fromDateTime(DateTimeImmutable $value): static
    {
        // normalized timezone
        return new static(
            $value->setTimezone(new DateTimeZone(static::ZONE))
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

    public function jsonSerialize(): int
    {
        return (int) $this->toString();
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
