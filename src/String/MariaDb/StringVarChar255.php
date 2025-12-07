<?php

declare(strict_types=1);

namespace PhpTypedValues\String\MariaDb;

use PhpTypedValues\Abstract\String\StrType;
use PhpTypedValues\Exception\StringTypeException;

/**
 * Database VARCHAR(255) string.
 *
 * Example "Hello world"
 *
 * @psalm-immutable
 */
class StringVarChar255 extends StrType
{
    /**
     * @readonly
     */
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
