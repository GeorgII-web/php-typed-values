<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\DateTime;

use const PHP_EOL;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\DateTimeTypeException;
use PhpTypedValues\Exception\ReasonableRangeDateTimeTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function count;
use function is_scalar;
use function is_string;
use function sprintf;

/**
 * Base implementation for DateTime typed values.
 *
 * Provides strict parsing with detailed error aggregation, round-trip
 * validation against the format, timezone normalization, and reasonable
 * timestamp range checks.
 *
 * Example
 *  - $v = MyDateTime::fromString('2025-01-02T03:04:05+00:00');
 *  - $v->toString(); // '2025-01-02T03:04:05+00:00'
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract readonly class DateTimeType extends PrimitiveType implements DateTimeTypeInterface
{
    protected const FORMAT = '';
    protected const MIN_TIMESTAMP_SECONDS = -62135596800; // 0001-01-01
    protected const MAX_TIMESTAMP_SECONDS = 253402300799; // 9999-12-31 23:59:59

    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @throws DateTimeTypeException
     * @throws ReasonableRangeDateTimeTypeException
     */
    abstract public function value(): DateTimeImmutable;

    /**
     * @throws ReasonableRangeDateTimeTypeException
     * @throws DateTimeTypeException
     */
    protected static function createFromFormat(
        string $value,
        string $format,
        ?DateTimeZone $timezone = null,
    ): DateTimeImmutable {
        if (str_contains($value, "\0")) {
            throw new DateTimeTypeException('Date time string must not contain null bytes');
        }

        if (trim($value) === '') {
            throw new DateTimeTypeException('Date time string must not be blank');
        }

        /**
         * Collect errors and throw exception with all of them.
         */
        $dt = DateTimeImmutable::createFromFormat($format, $value, $timezone);
        /**
         * Normalize getLastErrors result to an array with counters.
         * Some PHP versions return an array with zero counts instead of false.
         */
        $errors = DateTimeImmutable::getLastErrors() ?: [
            'errors' => [],
            'warnings' => [],
        ];

        if (count($errors['errors']) > 0 || count($errors['warnings']) > 0) {
            $errorMessages = '';

            foreach ($errors['errors'] as $pos => $message) {
                $errorMessages .= sprintf('Error at %d: %s' . PHP_EOL, $pos, $message);
            }

            foreach ($errors['warnings'] as $pos => $message) {
                $errorMessages .= sprintf('Warning at %d: %s' . PHP_EOL, $pos, $message);
            }

            throw new DateTimeTypeException(sprintf('Invalid date time value "%s", use format "%s"', $value, static::FORMAT) . PHP_EOL . $errorMessages);
        }

        /**
         * Strict “round-trip” check.
         *
         * @psalm-suppress PossiblyFalseReference
         */
        if ($value !== $dt->format(static::FORMAT)) {
            throw new DateTimeTypeException(sprintf('Unexpected conversion, source string %s is not equal to formatted one %s', $value, $dt->format(static::FORMAT)));
        }

        /**
         * Assert that timestamp in a reasonable range.
         *
         * @psalm-suppress PossiblyFalseReference
         */
        $ts = $dt->format('U');
        if ($ts < static::MIN_TIMESTAMP_SECONDS || $ts > static::MAX_TIMESTAMP_SECONDS) {
            throw new ReasonableRangeDateTimeTypeException(sprintf('Timestamp "%s" out of supported range "%d"-"%d".', $ts, static::MIN_TIMESTAMP_SECONDS, static::MAX_TIMESTAMP_SECONDS));
        }

        /**
         * $dt is not FALSE here, it will fail before on error checking.
         * Reset to a default time zone.
         *
         * @psalm-suppress FalsableReturnStatement
         */
        return $dt->setTimezone(new DateTimeZone(self::DEFAULT_ZONE));
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
            $result = match (true) {
                is_string($value) => static::fromString($value, $timezone),
                ($value instanceof DateTimeImmutable) => static::fromDateTime($value),
                //                ($value instanceof self) => static::fromDateTime($value->value()),
                $value instanceof Stringable, is_scalar($value) => static::fromString((string) $value, $timezone),
                default => throw new TypeException('Value cannot be cast to date time'),
            };

            /** @var static */
            return $result;
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
            $result = static::fromString($value, $timezone);

            /* @var static */
            return $result;
        } catch (Exception) {
            /* @var PrimitiveType */
            return $default;
        }
    }
}
