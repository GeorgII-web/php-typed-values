<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime\Timestamp;

use DateTimeZone;
use PhpTypedValues\Code\DateTime\DateTimeType;
use PhpTypedValues\Code\Exception\DateTimeTypeException;

use function intdiv;
use function sprintf;

/**
 * Unix timestamp in milliseconds since Unix epoch (UTC), e.g. "1732445696123".
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
                self::FORMAT,
                new DateTimeZone(static::ZONE)
            )
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
}
