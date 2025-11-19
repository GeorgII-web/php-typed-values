<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use PhpTypedValues\Code\Exception\NumericTypeException;
use PhpTypedValues\Code\Integer\IntType;

/**
 * @psalm-immutable
 */
final readonly class IntegerBasic extends IntType
{
    protected int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

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

    public function value(): int
    {
        return $this->value;
    }
}
