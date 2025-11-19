<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use PhpTypedValues\Code\Assert\Assert;
use PhpTypedValues\Code\Exception\NumericTypeException;
use PhpTypedValues\Code\Integer\IntType;

/**
 * @psalm-immutable
 */
final readonly class WeekDayInt extends IntType
{
    /** @var int<1, 7> */
    protected int $value;

    /**
     * @throws NumericTypeException
     */
    public function __construct(int $value)
    {
        Assert::greaterThanEq($value, 1, 'Value must be between 1 and 7');
        Assert::lessThanEq($value, 7, 'Value must be between 1 and 7');

        /**
         * @var int<1, 7> $value
         */
        $this->value = $value;
    }

    /**
     * @return int<1, 7>
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * @throws NumericTypeException
     */
    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    /**
     * @throws NumericTypeException
     */
    public static function fromString(string $value): self
    {
        parent::assertNumericString($value);

        return new self((int) $value);
    }
}
