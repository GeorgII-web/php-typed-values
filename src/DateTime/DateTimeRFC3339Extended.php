<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime;

use const DATE_RFC3339_EXTENDED;

use DateTimeImmutable;
use Exception;
use PhpTypedValues\Base\Primitive\DateTime\DateTimeTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\DateTime\DateTimeTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_string;

/**
 * Date-time value formatted using PHP's DATE_RFC3339_EXTENDED (RFC 3339 with microseconds).
 *
 * Inherits strict parsing and validation from DateTimeType, including detailed
 * parser error aggregation and exact round-trip checks. Ensures output
 * formatting with DATE_RFC3339_EXTENDED, preserving fractional seconds and
 * timezone offset.
 *
 * Example
 *  - $v = DateTimeRFC3339Extended::fromString('2025-01-02T03:04:05.123456+00:00');
 *    $v->toString(); // "2025-01-02T03:04:05.123456+00:00"
 *  - $v = DateTimeRFC3339Extended::fromDateTime(new DateTimeImmutable('2030-12-31T23:59:59.654321+03:00'));
 *    (string) $v; // "2030-12-31T23:59:59.654321+03:00"
 *
 * @psalm-immutable
 */
class DateTimeRFC3339Extended extends DateTimeTypeAbstract
{
    protected const FORMAT = DATE_RFC3339_EXTENDED;

    /**
     * @readonly
     */
    protected DateTimeImmutable $value;

    public function __construct(DateTimeImmutable $value)
    {
        // normalized time zone
        $this->value = $value->setTimezone(static::stringToDateTimeZone(static::DEFAULT_ZONE));
    }

    /**
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
                case $value instanceof DateTimeImmutable:
                    return static::fromDateTime($value);
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
     * @return static
     */
    public function withTimeZone(string $timezone)
    {
        return new static(
            $this->value()->setTimezone(static::stringToDateTimeZone($timezone))
        );
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
