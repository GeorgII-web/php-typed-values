<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use PhpTypedValues\Base\Primitive\String\StrType;

/**
 * Generic string typed value.
 *
 * Wraps any PHP string without additional validation and provides
 * convenient factory and formatting helpers.
 *
 * Example
 *  - $v = StringStandard::fromString('hello');
 *    $v->toString(); // "hello"
 *  - (string) StringStandard::fromString('x'); // "x"
 *
 * @psalm-immutable
 */
readonly class StringStandard extends StrType
{
    protected string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

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
        return $this->value === '';
    }

    public function isUndefined(): bool
    {
        return false;
    }
}
