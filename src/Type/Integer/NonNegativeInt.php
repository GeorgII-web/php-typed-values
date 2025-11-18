<?php

declare(strict_types=1);

namespace PhpTypedValues\Type\Integer;

use PhpTypedValues\BaseType\BaseIntType;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 */
final readonly class NonNegativeInt extends BaseIntType
{
    /** @var non-negative-int */
    protected int $value;

    public function __construct(int $value)
    {
        Assert::greaterThanEq($value, 0, 'Value must be a non-negative integer');

        $this->value = max(0, $value);
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

    /**
     * @return non-negative-int
     */
    public function value(): int
    {
        return $this->value;
    }
}
