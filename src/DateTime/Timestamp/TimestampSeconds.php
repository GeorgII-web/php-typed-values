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

use function is_int;
use function is_string;

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
     * @readonly
     */
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
     * @return static
     */
    public static function fromInt(int $value, string $timezone = self::DEFAULT_ZONE)
    {
        return static::fromString((string) $value, $timezone);
    }

    /**
     * Parse from a numeric Unix timestamp string (seconds).
     *
     * @param non-empty-string $timezone
     *
     * @throws DateTimeTypeException
     * @return static
     */
    public static function fromString(string $value, string $timezone = self::DEFAULT_ZONE)
    {
        return new static(
            static::getDateTimeFromFormatedString(
                $value,
                static::FORMAT,
                new DateTimeZone($timezone)
            )
        );
    }

    /**
     * @return static
     */
    public static function fromDateTime(DateTimeImmutable $value)
    {
        return new static($value);
    }

    /**
     * @return static
     */
    public function withTimeZone(string $timezone)
    {
        return new static(
            $this->value()->setTimezone(new DateTimeZone($timezone))
        );
    }

    public function toString(): string
    {
        return $this->value()->format(static::FORMAT);
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
     * @param mixed $value
     */
    public static function tryFromMixed(
        $value,
        string $timezone = self::DEFAULT_ZONE,
        PrimitiveType $default = null
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
                case $value instanceof DateTimeImmutable:
                    return static::fromDateTime($value);
                default:
                    throw new TypeException('Value cannot be cast to date time');
            }
        } catch (Exception $exception) {
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
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static $result */
            return static::fromString($value, $timezone);
        } catch (Exception $exception) {
            /* @var PrimitiveType */
            return $default;
        }
    }
}
