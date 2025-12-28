<?php

declare(strict_types=1);

namespace PhpTypedValues\String\MariaDb;

use PhpTypedValues\Base\Primitive\String\StrType;
use PhpTypedValues\Exception\StringTypeException;

/**
 * MariaDB VARCHAR(255) string.
 *
 * Accepts any string with length up to 255 characters (mb_strlen based). The
 * original string is preserved on success; longer values are rejected.
 *
 * Example
 *  - $v = StringVarChar255::fromString('Hello world');
 *    $v->toString(); // 'Hello world'
 *  - StringVarChar255::fromString(str_repeat('x', 256)); // throws StringTypeException
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
