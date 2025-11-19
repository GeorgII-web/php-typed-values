<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use PhpTypedValues\Code\Assert\Assert;
use PhpTypedValues\Code\BaseType\BaseIntType;
use PhpTypedValues\Code\Exception\TypeException;

/**
 * @psalm-immutable
 */
final readonly class NonNegativeInt extends BaseIntType
{
    /** @var non-negative-int */
    protected int $value;

    /**
     * @throws TypeException
     */
    public function __construct(int $value)
    {
        Assert::greaterThanEq($value, 0, 'Value must be a non-negative integer');

        /**
         * @var non-negative-int $value
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
     * @return non-negative-int
     */
    public function value(): int
    {
        return $this->value;
    }
}
