<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use PhpTypedValues\Code\Assert\Assert;
use PhpTypedValues\Code\Exception\StringTypeException;
use PhpTypedValues\Code\String\StrType;

/**
 * @psalm-immutable
 */
readonly class NonEmptyStr extends StrType
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws StringTypeException
     */
    public function __construct(string $value)
    {
        Assert::nonEmptyString($value);

        /** @var non-empty-string $value */
        $this->value = $value;
    }

    /**
     * @throws StringTypeException
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
