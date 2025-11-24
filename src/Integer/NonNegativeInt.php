<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use PhpTypedValues\Code\Exception\IntegerTypeException;
use PhpTypedValues\Code\Integer\IntType;

use function sprintf;

/**
 * @psalm-immutable
 */
readonly class NonNegativeInt extends IntType
{
    /** @var non-negative-int */
    protected int $value;

    /**
     * @throws IntegerTypeException
     */
    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new IntegerTypeException(sprintf('Expected non-negative integer, got "%d"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws IntegerTypeException
     */
    public static function fromInt(int $value): static
    {
        return new static($value);
    }

    /**
     * @throws IntegerTypeException
     */
    public static function fromString(string $value): static
    {
        parent::assertIntegerString($value);

        return new static((int) $value);
    }

    /**
     * @return non-negative-int
     */
    public function value(): int
    {
        return $this->value;
    }
}
