<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use PhpTypedValues\Code\Exception\IntegerTypeException;
use PhpTypedValues\Code\Integer\IntType;

/**
 * @psalm-immutable
 */
readonly class IntegerStandard extends IntType
{
    protected int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

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

    public function value(): int
    {
        return $this->value;
    }
}
