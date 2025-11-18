<?php

declare(strict_types=1);

namespace PhpTypedValues\Type\Integer;

use PhpTypedValues\BaseType\BaseIntType;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 */
final readonly class WeekDayInt extends BaseIntType
{
    /** @var int<1, 7> */
    protected int $value;

    public function __construct(int $value)
    {
        Assert::greaterThanEq($value, 1, 'Value must be between 1 and 7');
        Assert::lessThanEq($value, 7, 'Value must be between 1 and 7');

        $this->value = max(1, min($value, 7));
    }

    /**
     * @return int<1, 7>
     */
    public function value(): int
    {
        return $this->value;
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
}
