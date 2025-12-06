<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use PhpTypedValues\Abstract\String\StrType;

/**
 * Represents any PHP string.
 *
 * Example "hello"
 *
 * @psalm-immutable
 */
class StringStandard extends StrType
{
    /**
     * @readonly
     */
    protected string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static($value);
    }

    public function value(): string
    {
        return $this->value;
    }
}
