<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use PhpTypedValues\Code\Assert\Assert;
use PhpTypedValues\Code\BaseType\BaseIntType;
use PhpTypedValues\Code\Exception\TypeException;

/**
 * @psalm-immutable
 */
final readonly class PositiveInt extends BaseIntType
{
    /** @var positive-int */
    protected int $value;

    /**
     * @throws TypeException
     */
    public function __construct(int $value)
    {
        Assert::greaterThanEq($value, 1, 'Value must be a positive integer');

        /**
         * @var positive-int $value
         */
        $this->value = $value;
    }

    /**
     * @throws TypeException
     */
    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    /**
     * @throws TypeException
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
