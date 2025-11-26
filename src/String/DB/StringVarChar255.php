<?php

declare(strict_types=1);

namespace PhpTypedValues\String\DB;

use PhpTypedValues\Code\Exception\StringTypeException;
use PhpTypedValues\Code\String\StrType;

/**
 * Database VARCHAR(255) string.
 *
 * Example "Hello world"
 *
 * @psalm-immutable
 */
readonly class StringVarChar255 extends StrType
{
    protected string $value;

    /**
     * @throws StringTypeException
     */
    public function __construct(string $value)
    {
        if (mb_strlen($value) > 255) {
            throw new StringTypeException('String is too long, max 255 chars allowed');
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

    public function value(): string
    {
        return $this->value;
    }
}
