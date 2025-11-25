<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime\Timestamp;

use DateTimeZone;
use PhpTypedValues\Code\DateTime\DateTimeType;
use PhpTypedValues\Code\Exception\DateTimeTypeException;

/**
 * Unix timestamp (seconds since Unix epoch, UTC), e.g. "1732445696".
 *
 * @psalm-immutable
 */
readonly class TimestampSeconds extends DateTimeType
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
    public static function fromString(string $value): static
    {
        return new static(
            static::createFromFormat(
                $value,
                static::FORMAT,
                new DateTimeZone(static::ZONE)
            )
        );
    }

    public function toString(): string
    {
        return $this->value()->format(static::FORMAT);
    }
}
