<?php

declare(strict_types=1);

namespace PhpTypedValues\String\MariaDb;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Base\Primitive\String\StrType;
use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_scalar;
use function is_string;
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

    public function isTypeOf(string ...$classNames): bool
    {
        foreach ($classNames as $className) {
            if ($this instanceof $className) {
                return true;
            }
        }

        return false;
    }

    public function toString(): string
    {
        return $this->value();
    }

    public function isEmpty(): bool
    {
        return $this->value === '';
    }

    public function isUndefined(): bool
    {
        return false;
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType {
        try {
            /** @var static */
            return match (true) {
                is_string($value) => static::fromString($value),
                //                ($value instanceof self) => static::fromString($value->value()),
                $value instanceof Stringable, is_scalar($value) => static::fromString((string) $value),
                null === $value => static::fromString(''),
                default => throw new TypeException('Value cannot be cast to string'),
            };
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }
}
