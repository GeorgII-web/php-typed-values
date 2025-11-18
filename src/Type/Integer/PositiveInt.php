<?php

declare(strict_types=1);

namespace PhpTypedValues\Type\Integer;

use PhpTypedValues\BaseType\BaseIntType;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 */
final readonly class PositiveInt extends BaseIntType
{
    /** @var positive-int */
    protected int $value;

    public function __construct(int $value)
    {
        Assert::positiveInteger($value, 'Value must be a positive integer');

        $this->value = max(1, $value);
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
     * @return positive-int
     */
    public function value(): int
    {
        return $this->value;
    }
}
