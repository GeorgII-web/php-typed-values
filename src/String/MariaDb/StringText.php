<?php

declare(strict_types=1);

namespace PhpTypedValues\String\MariaDb;

use PhpTypedValues\Base\Primitive\String\StrType;
use PhpTypedValues\Exception\StringTypeException;

use function mb_strlen;

/**
 * MariaDB TEXT string (up to 65,535 characters).
 *
 * Accepts any string including empty, as long as its length measured by
 * mb_strlen() is not greater than 65,535 characters.
 *
 * Example
 *  - $t = StringText::fromString('lorem ipsum');
 *    $t->toString(); // 'lorem ipsum'
 *  - StringText::fromString(str_repeat('x', 65536)); // throws StringTypeException
 *
 * @psalm-immutable
 */
class StringText extends StrType
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
        if (mb_strlen($value) > 65535) {
            throw new StringTypeException('String is too long, max 65535 chars allowed');
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
