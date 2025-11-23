<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use PhpTypedValues\Code\Assert\Assert;
use PhpTypedValues\Code\Exception\NumericTypeException;
use PhpTypedValues\Code\Integer\IntType;

/**
 * @psalm-immutable
 */
readonly class NonNegativeInt extends IntType
{
    /** @var non-negative-int */
    protected int $value;

    /**
     * @throws NumericTypeException
     */
    public function __construct(int $value)
    {
        Assert::greaterThanEq($value, 0);

        /**
         * @var non-negative-int $value
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
     * @return non-negative-int
     */
    public function value(): int
    {
        return $this->value;
    }
}
