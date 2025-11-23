<?php

declare(strict_types=1);

namespace PhpTypedValues\Code\DateTime;

use const PHP_EOL;

use DateTimeImmutable;
use DateTimeZone;
use PhpTypedValues\Code\Exception\DateTimeTypeException;

use function count;
use function sprintf;

/**
 * @psalm-immutable
 */
abstract readonly class DateTimeType implements DateTimeTypeInterface
{
    protected const FORMAT = '';
    protected const ZONE = 'UTC';

    protected DateTimeImmutable $value;

    /**
     * @throws DateTimeTypeException
     */
    protected static function createFromFormat(
        string $value,
        string $format,
        ?DateTimeZone $timezone = null,
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

    abstract public static function fromDateTime(DateTimeImmutable $value): self;

    public function value(): DateTimeImmutable
    {
        return $this->value;
    }

    public static function getFormat(): string
    {
        return static::FORMAT;
    }
}
