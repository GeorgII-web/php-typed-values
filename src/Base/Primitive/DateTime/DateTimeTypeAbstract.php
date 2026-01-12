<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\DateTime;

use const PHP_EOL;

use DateTimeImmutable;
use DateTimeZone;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\DateTime\DateTimeTypeException;
use PhpTypedValues\Exception\DateTime\ReasonableRangeDateTimeTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

use function count;
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
abstract readonly class DateTimeTypeAbstract extends PrimitiveTypeAbstract implements DateTimeTypeInterface
{
    //    protected const string FORMAT = '';
    //    protected const int MAX_TIMESTAMP_SECONDS = 253402300799; // 9999-12-31 23:59:59
    //    protected const int MIN_TIMESTAMP_SECONDS = -62135596800; // 0001-01-01

    abstract public static function fromDateTime(DateTimeImmutable $value): static;

    /**
     * @param non-empty-string $timezone
     */
    abstract public static function fromString(string $value, string $timezone = DateTimeTypeInterface::DEFAULT_ZONE): static;

    abstract public static function getFormat(): string;

    abstract public function isTypeOf(string ...$classNames): bool;

    abstract public function toString(): string;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T                $default
     * @param non-empty-string $timezone
     *
     * @return static|T
     */
    abstract public static function tryFromMixed(
        mixed $value,
        string $timezone = DateTimeTypeInterface::DEFAULT_ZONE,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T                $default
     * @param non-empty-string $timezone
     *
     * @return static|T
     */
    abstract public static function tryFromString(
        string $value,
        string $timezone = DateTimeTypeInterface::DEFAULT_ZONE,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract;

    abstract public function value(): DateTimeImmutable;

    /**
     * @param non-empty-string $timezone
     */
    abstract public function withTimeZone(string $timezone): static;

    /**
     * @throws ReasonableRangeDateTimeTypeException
     * @throws DateTimeTypeException
     */
    protected static function stringToDateTime(
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

            throw new DateTimeTypeException(sprintf('Invalid date time value "%s", use format "%s"', $value, $format) . PHP_EOL . $errorMessages);
        }

        /**
         * Strict “round-trip” check.
         *
         * @psalm-suppress PossiblyFalseReference
         */
        if ($value !== $dt->format($format)) {
            throw new DateTimeTypeException(sprintf('Unexpected conversion, source string "%s" is not equal to formatted one "%s"', $value, $dt->format($format)));
        }

        /**
         * Assert that timestamp in a reasonable range.
         *
         * @psalm-suppress PossiblyFalseReference
         */
        $ts = $dt->format('U');
        if ($ts < self::MIN_TIMESTAMP_SECONDS || $ts > self::MAX_TIMESTAMP_SECONDS) {
            throw new ReasonableRangeDateTimeTypeException(sprintf('Timestamp "%s" out of supported range "%d"-"%d".', $ts, self::MIN_TIMESTAMP_SECONDS, self::MAX_TIMESTAMP_SECONDS));
        }

        /**
         * $dt is not FALSE here, it will fail before on error checking.
         * Reset to a default time zone.
         *
         * @psalm-suppress FalsableReturnStatement
         */
        $defaultZone = DateTimeTypeInterface::DEFAULT_ZONE;

        return $dt->setTimezone(static::stringToDateTimeZone($defaultZone));
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
