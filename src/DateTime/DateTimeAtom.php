<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime;

use const DATE_ATOM;

use DateTimeImmutable;
use DateTimeZone;
use PhpTypedValues\Code\DateTime\DateTimeType;
use PhpTypedValues\Code\Exception\DateTimeTypeException;

/**
 * ATOM RFC 3339 format based on ISO 8601.
 *
 * @psalm-immutable
 */
readonly class DateTimeAtom extends DateTimeType
{
    protected const FORMAT = DATE_ATOM;

    /**
     * @throws DateTimeTypeException
     */
    public static function fromString(string $value): self
    {
        return new self(
            self::createFromFormat(
                $value,
                self::FORMAT,
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
