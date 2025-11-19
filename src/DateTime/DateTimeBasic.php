<?php

declare(strict_types=1);

namespace PhpTypedValues\DateTime;

use DateTimeImmutable;
use PhpTypedValues\Code\DateTime\DateTimeType;
use PhpTypedValues\Code\Exception\DateTimeTypeException;

/**
 * @psalm-immutable
 */
final readonly class DateTimeBasic extends DateTimeType
{
    protected DateTimeImmutable $value;

    public function __construct(DateTimeImmutable $value)
    {
        $this->value = $value;
    }

    public static function fromDateTime(DateTimeImmutable $value): self
    {
        return new self($value);
    }

    /**
     * @throws DateTimeTypeException
     */
    public static function fromString(string $value): self
    {
        $dt = parent::parseString($value);

        return new self($dt);
    }

    public function value(): DateTimeImmutable
    {
        return $this->value;
    }
}
