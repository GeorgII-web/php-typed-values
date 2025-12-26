<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use PhpTypedValues\Base\Primitive\String\StrType;
use PhpTypedValues\Exception\StringTypeException;

use function sprintf;
use function trim;

/**
 * Non-blank string typed value (not empty and not only whitespace).
 *
 * Trims the input for validation purposes and rejects strings that are empty
 * after trimming (e.g., " ", "\n\t"). The original value is preserved.
 *
 * Example
 *  - $v = StringNonBlank::fromString(' hello ');
 *    $v->toString(); // ' hello '
 *  - StringNonBlank::fromString("   "); // throws StringTypeException
 *
 * @psalm-immutable
 */
readonly class StringNonBlank extends StrType
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws StringTypeException
     */
    public function __construct(string $value)
    {
        if (trim($value) === '') {
            throw new StringTypeException(sprintf('Expected non-blank string, got "%s"', $value));
        }

        /** @var non-empty-string $value */
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

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->value();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }
}
