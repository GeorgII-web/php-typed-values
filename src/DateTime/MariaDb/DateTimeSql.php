<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime\MariaDb;

use DateTimeImmutable;
use Exception;
use PhpTypedValues\Base\Primitive\DateTime\DateTimeTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\DateTime\DateTimeTypeException;
use PhpTypedValues\Exception\DateTime\ZoneDateTimeTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_string;

/**
 * Date-time value formatted using 'Y-m-d H:i:s' (SQL\Human format).
 *
 * Provides strict parsing with detailed error aggregation via the base
 * DateTimeType, preserves the exact offset, and guarantees round-trip
 * formatting using SQL format.
 *
 * Example
 *  - $v = DateTimeSql::fromString('2025-01-02 03:04:05');
 *    $v->toString(); // "2025-01-02 03:04:05"
 *  - $v = DateTimeSql::fromDateTime(new DateTimeImmutable('2030-12-31 23:59:59'));
 *    (string) $v; // "2030-12-31 23:59:59"
 *
 * @psalm-immutable
 */
readonly class DateTimeSql extends DateTimeTypeAbstract
{
    public const string FORMAT = 'Y-m-d H:i:s';

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
     */
    public static function fromDateTime(DateTimeImmutable $value): static
    {
        return new static($value);
    }

    /**
     * @param non-empty-string $timezone
     *
     * @throws DateTimeTypeException
     *
     * @psalm-pure
     */
    public static function fromString(string $value, string $timezone = self::DEFAULT_ZONE): static
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

    public function jsonSerialize(): string
    {
        return $this->toString();
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
     */
    public static function tryFromMixed(
        mixed $value,
        string $timezone = self::DEFAULT_ZONE,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static $result */
            return match (true) {
                is_string($value) => static::fromString($value, $timezone),
                ($value instanceof DateTimeImmutable) => static::fromDateTime($value),
                $value instanceof Stringable => static::fromString((string) $value, $timezone),
                default => throw new TypeException('Value cannot be cast to date time'),
            };
        } catch (Exception) {
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
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static $result */
            return static::fromString($value, $timezone);
        } catch (Exception) {
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
     */
    public function withTimeZone(string $timezone): static
    {
        /** @psalm-suppress ImpureVariable */
        return new static(
            $this->value()->setTimezone(static::stringToDateTimeZone($timezone))
        );
    }
}
