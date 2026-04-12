<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime;

use const DATE_RFC2822;

use DateTimeImmutable;
use Exception;
use PhpTypedValues\Base\Primitive\DateTime\DateTimeTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\DateTime\DateTimeTypeException;
use PhpTypedValues\Exception\DateTime\RFC2822DateTimeTypeException;
use PhpTypedValues\Exception\DateTime\ZoneDateTimeTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_string;

/**
 * Date-time value formatted using PHP's DATE_RFC2822 (RFC 2822).
 *
 * Provides strict parsing with detailed error aggregation via the base
 * DateTimeType, preserves the exact offset, and guarantees round-trip
 * formatting using DATE_RFC2822.
 *
 * Example
 *  - $v = DateTimeRFC2822::fromString('Mon, 15 Aug 2005 15:52:01 +0000');
 *    $v->toString(); // "Mon, 15 Aug 2005 15:52:01 +0000"
 *
 * @psalm-immutable
 */
class DateTimeRFC2822 extends DateTimeTypeAbstract
{
    /**
     * @var string
     */
    public const FORMAT = DATE_RFC2822;

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
     * @throws RFC2822DateTimeTypeException
     * @return never
     * @param null $value
     */
    public static function fromNull($value)
    {
        throw new RFC2822DateTimeTypeException('DateTimeRFC2822 type cannot be created from null');
    }

    /**
     * @param non-empty-string $timezone
     *
     * @throws RFC2822DateTimeTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromString(string $value, string $timezone = self::DEFAULT_ZONE)
    {
        try {
            return new static(
                static::stringToDateTime(
                    $value,
                    static::FORMAT,
                    static::stringToDateTimeZone($timezone)
                )
            );
        } catch (DateTimeTypeException $e) {
            throw new RFC2822DateTimeTypeException($e->getMessage(), $e->getCode(), $e);
        }
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

    /**
     * @throws RFC2822DateTimeTypeException
     * @return never
     */
    public static function toNull()
    {
        throw new RFC2822DateTimeTypeException('DateTimeRFC2822 type cannot be converted to null');
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
                case $value instanceof DateTimeImmutable:
                    return static::fromDateTime($value);
                case is_object($value) && method_exists($value, '__toString'):
                    return static::fromString((string) $value, $timezone);
                default:
                    throw new TypeException('Value cannot be cast to date time');
            }
        } catch (Exception $exception) {
            // @var PrimitiveTypeAbstract
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
            // @var PrimitiveTypeAbstract
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
