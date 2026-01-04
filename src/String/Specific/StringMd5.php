<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstractAbstract;
use PhpTypedValues\Exception\String\Md5StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;
use function is_scalar;
use function is_string;
use function md5;
use function preg_match;
use function sprintf;

/**
 * MD5 hash string (32 hexadecimal characters).
 *
 * Validates that the string is exactly 32 hexadecimal characters (case-insensitive).
 * The original case is preserved on successful validation.
 *
 * Example
 *  - $h = StringMd5::fromString('5d41402abc4b2a76b9719d911017c592');
 *    $h->value(); // '5d41402abc4b2a76b9719d911017c592'
 *  - StringMd5::fromString('invalid'); // throws Md5StringTypeException
 *
 * @psalm-immutable
 */
readonly class StringMd5 extends StringTypeAbstractAbstract
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws Md5StringTypeException
     */
    public function __construct(string $value)
    {
        if (preg_match('/^[a-fA-F0-9]{32}$/', $value) !== 1) {
            throw new Md5StringTypeException(sprintf('Expected MD5 hash (32 hex characters), got "%s"', $value));
        }

        /** @var non-empty-string $value */
        $this->value = $value;
    }

    /**
     * Creates an MD5 instance from an existing MD5 hash string.
     *
     * @throws Md5StringTypeException
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }

    /**
     * Creates an MD5 instance by hashing the provided input string.
     *
     * @param string $input The string to hash with MD5
     *
     * @throws Md5StringTypeException
     */
    public static function hash(string $input): static
    {
        return new static(md5($input));
    }

    /** @return non-empty-string */
    public function value(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value();
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

    /**
     * Returns the MD5 hash as a string.
     *
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->value();
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param PrimitiveTypeAbstract $default
     *
     * @return static|PrimitiveTypeAbstract
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return match (true) {
                is_string($value) => static::fromString($value),
                $value instanceof Stringable, is_scalar($value) => static::fromString((string) $value),
                default => throw new TypeException('Value cannot be cast to string'),
            };
        } catch (Exception) {
            /** @var PrimitiveTypeAbstract */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param PrimitiveTypeAbstract $default
     *
     * @return static|PrimitiveTypeAbstract
     */
    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
            /** @var PrimitiveTypeAbstract */
            return $default;
        }
    }
}
