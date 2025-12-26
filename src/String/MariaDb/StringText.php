<?php

declare(strict_types=1);

namespace PhpTypedValues\String\MariaDb;

use PhpTypedValues\Base\Primitive\String\StrType;
use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

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
readonly class StringText extends StrType
{
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
     * @template T
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromMixed(mixed $value, mixed $default = new Undefined()): mixed
    {
        try {
            return static::fromString(
                static::convertMixedToString($value)
            );
        } catch (TypeException) {
            return $default;
        }
    }

    /**
     * @template T
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(string $value, mixed $default = new Undefined()): mixed
    {
        try {
            return static::fromString($value);
        } catch (TypeException) {
            return $default;
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
