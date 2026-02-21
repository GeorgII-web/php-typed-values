<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime\Timestamp;

use DateTimeImmutable;
use Exception;
use PhpTypedValues\Base\Primitive\DateTime\DateTimeTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\DateTime\DateTimeTypeException;
use PhpTypedValues\Exception\DateTime\ZoneDateTimeTypeException;
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
class TimestampMilliseconds extends DateTimeTypeAbstract
{
    /**
     * Internal formatting pattern for seconds + microseconds.
     *
     * @see https://www.php.net/manual/en/datetime.format.php
     * @var string
     */
    public const FORMAT = 'U.v';

    /**
     * @readonly
     */
    protected DateTimeImmutable $value;

    /**
     * @throws ZoneDateTimeTypeException
     */
    public function __construct(DateTimeImmutable $value)
    {
        // normalized time zone
        $this->value = $value->setTimezone(static::stringToDateTimeZone(static::DEFAULT_ZONE));
    }

    /**
     * @throws ZoneDateTimeTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromDateTime(DateTimeImmutable $value)
    {
        return new static($value);
    }

    /**
     * @param non-empty-string $timezone
     *
     * @throws DateTimeTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromInt(int $value, string $timezone = self::DEFAULT_ZONE)
    {
        return static::fromString((string) $value, $timezone);
    }

    /**
     * Parse from a numeric Unix timestamp string (milliseconds).
     *
     * @param non-empty-string $timezone
     *
     * @throws DateTimeTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromString(string $value, string $timezone = self::DEFAULT_ZONE)
    {
        if (!ctype_digit($value)) {
            throw new DateTimeTypeException(sprintf('Expected milliseconds timestamp as digits, got "%s"', $value));
        }

        // "1732445696123" -> 1732445696 seconds, 123 milliseconds
        $milliseconds = (int) $value;
        $seconds = intdiv($milliseconds, 1000);
        $msRemainder = $milliseconds % 1000;

        // Build "seconds.milliseconds" string for INTERNAL_FORMAT, e.g. "1732445696.123"
        $secondsWithMilli = sprintf('%d.%03d', $seconds, $msRemainder);

        return new static(
            static::stringToDateTime(
                $secondsWithMilli,
                static::FORMAT,
                static::stringToDateTimeZone($timezone)
            )
        );
    }

    public static function getFormat(): string
    {
        return static::FORMAT;
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isTypeOf(string ...$classNames): bool
    {
        foreach ($classNames as $className) {
            if ($this instanceof $className) {
                return true;
            }
        }

        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    public function jsonSerialize(): int
    {
        return (int) $this->toString();
    }

    public function toInt(): int
    {
        return (int) $this->toString();
    }

    /**
     * Render as milliseconds since epoch, e.g. "1732445696123".
     */
    public function toString(): string
    {
        $dt = $this->value();

        $seconds = $dt->format('U');
        $millis = $dt->format('v');

        if ($seconds === '0') {
            return (string) (int) $millis;
        }

        return $seconds . $millis;
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T                $default
     * @param non-empty-string $timezone
     *
     * @return static|T
     *
     * @psalm-pure
     * @param mixed $value
     */
    public static function tryFromMixed(
        $value,
        string $timezone = self::DEFAULT_ZONE,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            switch (true) {
                case is_string($value):
                    return static::fromString($value, $timezone);
                case is_int($value):
                    return static::fromInt($value, $timezone);
                case is_object($value) && method_exists($value, '__toString'):
                    return static::fromString((string) $value, $timezone);
                default:
                    throw new TypeException('Value cannot be cast to date time');
            }
        } catch (Exception $exception) {
            /* @var PrimitiveTypeAbstract */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T                $default
     * @param non-empty-string $timezone
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromString(
        string $value,
        string $timezone = self::DEFAULT_ZONE,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static $result */
            return static::fromString($value, $timezone);
        } catch (Exception $exception) {
            /* @var PrimitiveTypeAbstract */
            return $default;
        }
    }

    public function value(): DateTimeImmutable
    {
        return $this->value;
    }

    /**
     * @psalm-pure
     *
     * @throws ZoneDateTimeTypeException
     * @return static
     */
    public function withTimeZone(string $timezone)
    {
        /** @psalm-suppress ImpureVariable */
        return new static(
            $this->value()->setTimezone(static::stringToDateTimeZone($timezone))
        );
    }
}
