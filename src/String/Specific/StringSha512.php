<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\Sha512StringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_scalar;
use function preg_match;
use function sprintf;

/**
 * SHA-512 hash string.
 *
 * Validates that the string is a valid 128-character hexadecimal SHA-512 hash.
 *
 * @psalm-immutable
 */
class StringSha512 extends StringTypeAbstract
{
    /** @var non-empty-string
     * @readonly */
    protected string $value;

    /**
     * @throws Sha512StringTypeException
     */
    public function __construct(string $value)
    {
        if ($value === '') {
            throw new Sha512StringTypeException('Expected non-empty SHA-512 hash');
        }

        if (!preg_match('/^[a-f0-9]{128}$/i', $value)) {
            throw new Sha512StringTypeException(sprintf('Expected valid SHA-512 hash, got "%s"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws Sha512StringTypeException
     * @throws Sha512StringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws Sha512StringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromDecimal(string $value)
    {
        return new static(static::decimalToString($value));
    }

    /**
     * @throws FloatTypeException
     * @throws Sha512StringTypeException
     * @throws Sha512StringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromFloat(float $value)
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws Sha512StringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws Sha512StringTypeException
     * @return never
     * @param null $value
     */
    public static function fromNull($value)
    {
        throw new Sha512StringTypeException('StringSha512 type cannot be created from null');
    }

    /**
     * @throws Sha512StringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static($value);
    }

    public function isEmpty(): bool
    {
        return false;
    }

    /**
     * @psalm-assert-if-true static $this
     */
    public function isTypeOf(string ...$classNames): bool
    {
        foreach ($classNames as $className) {
            if ($this instanceof $className) {
                return true;
            }
        }

        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    /**
     * @throws Sha512StringTypeException
     */
    public function toBool(): bool
    {
        return static::stringToBool($this->value);
    }

    /**
     * @throws DecimalTypeException
     */
    public function toDecimal(): string
    {
        return static::stringToDecimal($this->value);
    }

    /**
     * @throws Sha512StringTypeException
     * @throws FloatTypeException
     */
    public function toFloat(): float
    {
        return static::stringToFloat($this->value);
    }

    /**
     * @throws Sha512StringTypeException
     */
    public function toInt(): int
    {
        return static::stringToInt($this->value);
    }

    /**
     * @throws Sha512StringTypeException
     * @return never
     */
    public static function toNull()
    {
        throw new Sha512StringTypeException('StringSha512 type cannot be converted to null');
    }

    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @psalm-pure
     * @return \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract|static
     */
    public static function tryFromBool(bool $value, PrimitiveTypeAbstract $default = null)
    {
        $default ??= new Undefined();
        try {
            return static::fromBool($value);
        } catch (Sha512StringTypeException|StringTypeException $exception) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     * @return \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract|static
     */
    public static function tryFromDecimal(string $value, PrimitiveTypeAbstract $default = null)
    {
        $default ??= new Undefined();
        try {
            return static::fromDecimal($value);
        } catch (Sha512StringTypeException $exception) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     * @return \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract|static
     */
    public static function tryFromFloat(float $value, PrimitiveTypeAbstract $default = null)
    {
        $default ??= new Undefined();
        try {
            return static::fromFloat($value);
        } catch (FloatTypeException|Sha512StringTypeException|StringTypeException $exception) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     * @return \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract|static
     */
    public static function tryFromInt(int $value, PrimitiveTypeAbstract $default = null)
    {
        $default ??= new Undefined();
        try {
            return static::fromInt($value);
        } catch (Sha512StringTypeException $exception) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     * @return \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract|static
     * @param mixed $value
     */
    public static function tryFromMixed($value, PrimitiveTypeAbstract $default = null)
    {
        $default ??= new Undefined();
        if (is_scalar($value) || is_object($value) && method_exists($value, '__toString')) {
            try {
                return static::fromString((string) $value);
            } catch (Sha512StringTypeException $exception) {
                return $default;
            }
        }

        return $default;
    }

    /**
     * @psalm-pure
     * @return \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract|static
     */
    public static function tryFromString(string $value, PrimitiveTypeAbstract $default = null)
    {
        $default ??= new Undefined();
        try {
            return static::fromString($value);
        } catch (Sha512StringTypeException $exception) {
            return $default;
        }
    }

    /**
     * @return non-empty-string
     */
    public function value(): string
    {
        return $this->value;
    }
}
