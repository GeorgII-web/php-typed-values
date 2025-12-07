<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use PhpTypedValues\Abstract\String\StrType;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Represents any PHP string.
 *
 * Example "hello"
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

    public static function tryFromString(string $value): static|Undefined
    {
        return static::fromString($value);
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function value(): string
    {
        return $this->value;
    }
}
