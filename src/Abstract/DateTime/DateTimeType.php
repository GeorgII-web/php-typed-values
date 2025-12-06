<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\DateTime;

use const PHP_EOL;

use DateTimeImmutable;
use DateTimeZone;
use PhpTypedValues\Exception\DateTimeTypeException;
use PhpTypedValues\Exception\ReasonableRangeDateTimeTypeException;

use function count;
use function sprintf;

/**
 * @psalm-immutable
 */
abstract class DateTimeType implements DateTimeTypeInterface
{
    protected const FORMAT = '';
    protected const ZONE = 'UTC';
    protected const MIN_TIMESTAMP_SECONDS = -62135596800; // 0001-01-01
    protected const MAX_TIMESTAMP_SECONDS = 253402300799; // 9999-12-31 23:59:59
    /**
     * @readonly
     */
    protected DateTimeImmutable $value;

    /**
     * @throws DateTimeTypeException
     * @throws ReasonableRangeDateTimeTypeException
     */
    protected static function createFromFormat(
        string $value,
        string $format,
        ?DateTimeZone $timezone = null
    ): DateTimeImmutable {
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
         *
         * @psalm-suppress FalsableReturnStatement
         */
        return $dt;
    }

    public function __construct(DateTimeImmutable $value)
    {
        $this->value = $value;
    }

    /**
     * @return static
     */
    public static function fromDateTime(DateTimeImmutable $value)
    {
        // normalized timezone
        return new static(
            $value->setTimezone(new DateTimeZone(static::ZONE))
        );
    }

    public function value(): DateTimeImmutable
    {
        return $this->value;
    }

    public static function getFormat(): string
    {
        return static::FORMAT;
    }
}
