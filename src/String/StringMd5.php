<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Base\Primitive\String\StrType;
use PhpTypedValues\Exception\Md5StringTypeException;
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
 * Validates and stores a 32-character lowercase hexadecimal MD5 hash.
 * The value keeps its case on construction to ensure consistency.
 *
 * Example
 *  - $h = StringMd5::fromString('5d41402abc4b2a76b9719d911017c592');
 *    $h->value(); // '5d41402abc4b2a76b9719d911017c592'
 *  - StringMd5::fromString('invalid'); // throws Md5StringTypeException
 *
 * @psalm-immutable
 */
class StringMd5 extends StrType
{
    /** @var non-empty-string
     * @readonly */
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
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static($value);
    }

    /**
     * Creates an MD5 instance by hashing the provided input string.
     *
     * @param string $input The string to hash with MD5
     *
     * @throws Md5StringTypeException
     * @return static
     */
    public static function hash(string $input)
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

    /**
     * Returns the MD5 hash as a string.
     *
     * @return non-empty-string
     */
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
        return false;
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
     * @param mixed $value
     */
    public static function tryFromMixed(
        $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        try {
            switch (true) {
                case is_string($value):
                    return static::fromString($value);
                case is_object($value) && method_exists($value, '__toString'):
                case is_scalar($value):
                    return static::fromString((string) $value);
                default:
                    throw new TypeException('Value cannot be cast to string');
            }
        } catch (Exception $exception) {
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
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception $exception) {
            /** @var T */
            return $default;
        }
    }
}
