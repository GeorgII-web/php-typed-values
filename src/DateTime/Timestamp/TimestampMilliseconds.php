<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime\Timestamp;

use DateTimeImmutable;
use DateTimeZone;
use PhpTypedValues\Exception\DateTimeTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Internal\Primitive\DateTime\DateTimeType;
use PhpTypedValues\Undefined\Alias\Undefined;

use function intdiv;
use function sprintf;

/**
 * Unix timestamp value in milliseconds since the Unix epoch (UTC).
 *
 * Accepts a strictly numeric milliseconds string and converts it to an
 * internal DateTimeImmutable with microsecond precision. Parsing and
 * validation leverage DateTimeType, including detailed error aggregation and
 * strict round‑trip verification. Output is rendered back to milliseconds.
 *
 * Example
 *  - $v = TimestampMilliseconds::fromString('1732445696123');
 *    $v->toString(); // "1732445696123"
 *  - $v = TimestampMilliseconds::fromString('0');
 *    (string) $v; // "0"
 *
 * @psalm-immutable
 */
readonly class TimestampMilliseconds extends DateTimeType
{
    /**
     * Internal formatting pattern for seconds + microseconds.
     *
     * @see https://www.php.net/manual/en/datetime.format.php
     */
    protected const FORMAT = 'U.u';

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
     * Parse from a numeric Unix timestamp string (milliseconds).
     *
     * @throws DateTimeTypeException
     */
    public static function fromString(string $value): static
    {
        if (!ctype_digit($value)) {
            throw new DateTimeTypeException(sprintf('Expected milliseconds timestamp as digits, got "%s"', $value));
        }

        // "1732445696123" -> 1732445696 seconds, 123 milliseconds
        $milliseconds = (int) $value;
        $seconds = intdiv($milliseconds, 1000);
        $msRemainder = $milliseconds % 1000;

        // Convert the remainder to microseconds (pad to 3 digits, then * 1000)
        $microseconds = $msRemainder * 1000;

        // Build "seconds.microseconds" string for INTERNAL_FORMAT, e.g. "1732445696.123000"
        $secondsWithMicro = sprintf('%d.%06d', $seconds, $microseconds);

        return new static(
            static::createFromFormat(
                $secondsWithMicro,
                static::FORMAT,
                new DateTimeZone(static::ZONE)
            )
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

    public static function fromDateTime(DateTimeImmutable $value): static
    {
        // normalized timezone
        return new static(
            $value->setTimezone(new DateTimeZone(static::ZONE))
        );
    }

    /**
     * Render as milliseconds since epoch, e.g. "1732445696123".
     */
    public function toString(): string
    {
        $dt = $this->value();

        $seconds = (int) $dt->format('U');
        $micros = (int) $dt->format('u');

        // Using intdiv will throw a TypeError if $seconds is not an int, ensuring the cast is meaningful
        $milliseconds = (intdiv($seconds, 1) * 1000) + intdiv($micros, 1000);

        return (string) $milliseconds;
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

    public function isEmpty(): bool
    {
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }
}
