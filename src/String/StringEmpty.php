<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use PhpTypedValues\Base\Primitive\String\StrType;
use PhpTypedValues\Exception\StringTypeException;

use function sprintf;

/**
 * Empty string typed value.
 *
 * Ensures the wrapped string is empty (length == 0).
 *
 * Example
 *  - $v = StringEmpty::fromString('');
 *    $v->value(); // ''
 *  - StringEmpty::fromString('hello'); // throws StringTypeException
 *
 * @psalm-immutable
 */
readonly class StringEmpty extends StrType
{
    /**
     * @throws StringTypeException
     */
    public function __construct(string $value)
    {
        if ($value !== '') {
            throw new StringTypeException(sprintf('Expected empty string, got "%s"', $value));
        }
    }

    /**
     * @throws StringTypeException
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function value(): string
    {
        return '';
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
        return true;
    }

    public function isUndefined(): bool
    {
        return false;
    }
}
