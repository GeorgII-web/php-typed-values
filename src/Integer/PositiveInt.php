<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use PhpTypedValues\Code\Assert\Assert;
use PhpTypedValues\Code\Exception\NumericTypeException;
use PhpTypedValues\Code\Integer\IntType;

/**
 * @psalm-immutable
 */
final readonly class PositiveInt extends IntType
{
    /** @var positive-int */
    protected int $value;

    /**
     * @throws NumericTypeException
     */
    public function __construct(int $value)
    {
        Assert::greaterThanEq($value, 1);

        /**
         * @var positive-int $value
         */
        $this->value = $value;
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

    /**
     * @return positive-int
     */
    public function value(): int
    {
        return $this->value;
    }
}
