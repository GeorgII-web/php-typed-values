<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime;

use DateTimeImmutable;
use DateTimeZone;
use PhpTypedValues\Code\DateTime\DateTimeType;
use PhpTypedValues\Code\Exception\DateTimeTypeException;

/**
 * Unix timestamp (seconds since Unix epoch, UTC).
 *
 * @psalm-immutable
 */
readonly class DateTimeTimestamp extends DateTimeType
{
    /**
     * DateTime::format() pattern for Unix timestamp.
     *
     * @see https://www.php.net/manual/en/datetime.format.php
     */
    protected const FORMAT = 'U';

    /**
     * Parse from a numeric Unix timestamp string (seconds).
     *
     * @throws DateTimeTypeException
     */
    public static function fromString(string $value): self
    {
        return new self(
            self::createFromFormat(
                $value,
                static::FORMAT,
                new DateTimeZone(self::ZONE)
            )
        );
    }

    public function toString(): string
    {
        return $this->value()->format(self::FORMAT);
    }

    public static function fromDateTime(DateTimeImmutable $value): self
    {
        return new self($value);
    }
}
