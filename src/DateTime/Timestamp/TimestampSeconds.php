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
class TimestampSeconds extends DateTimeTypeAbstract
{
    /**
     * DateTime::format() pattern for Unix timestamp.
     *
     * @see https://www.php.net/manual/en/datetime.format.php
     * @var string
     */
    public const FORMAT = 'U';

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
     * Parse from a numeric Unix timestamp string (seconds).
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
        return new static(
            static::stringToDateTime(
                $value,
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

    public function toString(): string
    {
        return $this->value()->format(static::FORMAT);
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
                case $value instanceof DateTimeImmutable:
                    return static::fromDateTime($value);
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
     * @throws ZoneDateTimeTypeException
     * @return static
     */
    public function withTimeZone(string $timezone)
    {
        return new static(
            $this->value()->setTimezone(static::stringToDateTimeZone($timezone))
        );
    }
}
