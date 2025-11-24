<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use PhpTypedValues\Code\String\StrType;

/**
 * Represents any PHP string.
 *
 * @psalm-immutable
 */
readonly class StringBasic extends StrType
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
}
