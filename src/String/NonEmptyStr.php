<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use PhpTypedValues\Code\Assert\Assert;
use PhpTypedValues\Code\Exception\TypeException;
use PhpTypedValues\Code\String\StrType;

/**
 * @psalm-immutable
 */
final readonly class NonEmptyStr extends StrType
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws TypeException
     */
    public function __construct(string $value)
    {
        Assert::nonEmptyString($value, 'Value must be a non-empty string');

        /** @var non-empty-string $value */
        $this->value = $value;
    }

    /**
     * @throws TypeException
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /** @return non-empty-string */
    public function value(): string
    {
        return $this->value;
    }
}
