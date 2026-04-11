<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime;

use DateTimeImmutable;
use Exception;
use PhpTypedValues\Base\Primitive\DateTime\DateTimeTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\DateTime\DateTimeTypeException;
use PhpTypedValues\Exception\DateTime\Iso8601TimeTypeException;
use PhpTypedValues\Exception\DateTime\ZoneDateTimeTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_string;

/**
 * Time value formatted using ISO 8601 'H:i:s' pattern.
 *
 * Provides strict parsing with detailed error aggregation via the base
 * DateTimeType and guarantees round-trip formatting using 'H:i:s'.
 *
 * Example
 *  - $v = TimeIso8601::fromString('15:52:01');
 *    $v->toString(); // "15:52:01"
 *
 * @psalm-immutable
 */
class TimeIso8601 extends DateTimeTypeAbstract
{
    /**
     * @var string
     */
    public const FORMAT = 'H:i:s';

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
     * @throws Iso8601TimeTypeException
     * @return never
     */
    public static function fromNull(null $value)
    {
        throw new Iso8601TimeTypeException('TimeIso8601 type cannot be created from null');
    }

    /**
     * @param non-empty-string $timezone
     *
     * @throws Iso8601TimeTypeException
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
            throw new Iso8601TimeTypeException($e->getMessage(), $e->getCode(), $e);
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
     * @throws Iso8601TimeTypeException
     * @return never
     */
    public static function toNull()
    {
        throw new Iso8601TimeTypeException('TimeIso8601 type cannot be converted to null');
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
