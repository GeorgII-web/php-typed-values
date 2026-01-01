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
     * Parse from a numeric Unix timestamp string (seconds).
     *
     * @param non-empty-string $timezone
     *
     * @throws DateTimeTypeException
     */
    public static function fromString(string $value, string $timezone = self::DEFAULT_ZONE): static
    {
        return new static(
            static::getDateTimeFromFormatedString(
                $value,
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
                $value instanceof Stringable => static::fromString((string) $value, $timezone),
                ($value instanceof DateTimeImmutable) => static::fromDateTime($value),
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
