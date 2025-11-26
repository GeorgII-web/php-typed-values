<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use PhpTypedValues\Code\Exception\StringTypeException;
use PhpTypedValues\Code\String\StrType;

use function sprintf;

/**
 * @psalm-immutable
 */
readonly class StringNonEmpty extends StrType
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws StringTypeException
     */
    public function __construct(string $value)
    {
        if ($value === '') {
            throw new StringTypeException(sprintf('Expected non-empty string, got "%s"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws StringTypeException
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }

    /** @return non-empty-string */
    public function value(): string
    {
        return $this->value;
    }
}
