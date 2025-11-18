<?php

declare(strict_types=1);

namespace PhpTypedValues\Type\Integer;

use PhpTypedValues\BaseType\BaseIntType;

/**
 * @psalm-immutable
 */
final readonly class Integer extends BaseIntType
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
