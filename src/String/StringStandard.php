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
 * Methods
 *
 * @method        string       value()
 * @method static static|mixed tryFromString(string $value, mixed $default = null)
 * @method static static|mixed tryFromMixed(mixed $value, mixed $default = null)
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
