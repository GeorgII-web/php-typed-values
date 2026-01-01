<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime\Timestamp;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use PhpTypedValues\Base\Primitive\DateTime\DateTimeType;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\DateTimeTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function intdiv;
use function is_int;
use function is_string;
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
        // normalized time zone
        $this->value = $value->setTimezone(new DateTimeZone(static::DEFAULT_ZONE));
    }

    /**
     * @param non-empty-string $timezone
     *
     * @throws DateTimeTypeException
     */
    public static function fromInt(int $value, string $timezone = self::DEFAULT_ZONE): static
    {
        return static::fromString((string) $value, $timezone);
    }

    /**
     * Parse from a numeric Unix timestamp string (milliseconds).
     *
     * @param non-empty-string $timezone
     *
     * @throws DateTimeTypeException
     */
    public static function fromString(string $value, string $timezone = self::DEFAULT_ZONE): static
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
            static::getDateTimeFromFormatedString(
                $secondsWithMicro,
                static::FORMAT,
                new DateTimeZone($timezone)
            )
        );
    }

    public static function fromDateTime(DateTimeImmutable $value): static
    {
        return new static($value);
    }

    public function withTimeZone(string $timezone): static
    {
        return new static(
            $this->value()->setTimezone(new DateTimeZone($timezone))
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

    public function toInt(): int
    {
        return (int) $this->toString();
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

    /**
     * @template T of PrimitiveType
     *
     * @param T                $default
     * @param non-empty-string $timezone
     *
     * @return static|T
     */
    public static function tryFromMixed(
        mixed $value,
        string $timezone = self::DEFAULT_ZONE,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType {
        try {
            /** @var static $result */
            return match (true) {
                is_string($value) => static::fromString($value, $timezone),
                is_int($value) => static::fromInt($value, $timezone),
                //                ($value instanceof DateTimeImmutable) => static::fromDateTime($value),
                $value instanceof Stringable => static::fromString((string) $value, $timezone),
                default => throw new TypeException('Value cannot be cast to date time'),
            };
        } catch (Exception) {
            /* @var PrimitiveType */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T                $default
     * @param non-empty-string $timezone
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        string $timezone = self::DEFAULT_ZONE,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType {
        try {
            /** @var static $result */
            return static::fromString($value, $timezone);
        } catch (Exception) {
            /* @var PrimitiveType */
            return $default;
        }
    }
}
