<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\Sha256StringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_scalar;
use function preg_match;
use function sprintf;

/**
 * SHA-256 hash string.
 *
 * Validates that the string is a valid 64-character hexadecimal SHA-256 hash.
 *
 * @psalm-immutable
 */
readonly class StringSha256 extends StringTypeAbstract
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws Sha256StringTypeException
     */
    public function __construct(string $value)
    {
        if ($value === '') {
            throw new Sha256StringTypeException('Expected non-empty SHA-256 hash');
        }

        if (!preg_match('/^[a-f0-9]{64}$/i', $value)) {
            throw new Sha256StringTypeException(sprintf('Expected valid SHA-256 hash, got "%s"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws StringTypeException
     * @throws Sha256StringTypeException
     *
     * @psalm-pure
     */
    public static function fromBool(bool $value): static
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws Sha256StringTypeException
     *
     * @psalm-pure
     */
    public static function fromDecimal(string $value): static
    {
        return new static(static::decimalToString($value));
    }

    /**
     * @throws FloatTypeException
     * @throws Sha256StringTypeException
     * @throws StringTypeException
     *
     * @psalm-pure
     */
    public static function fromFloat(float $value): static
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws Sha256StringTypeException
     *
     * @psalm-pure
     */
    public static function fromInt(int $value): static
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws Sha256StringTypeException
     *
     * @psalm-pure
     */
    public static function fromString(string $value): static
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
     * @throws StringTypeException
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
     * @throws StringTypeException
     * @throws FloatTypeException
     */
    public function toFloat(): float
    {
        return static::stringToFloat($this->value);
    }

    /**
     * @throws StringTypeException
     */
    public function toInt(): int
    {
        return static::stringToInt($this->value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @psalm-pure
     */
    public static function tryFromBool(bool $value, PrimitiveTypeAbstract $default = new Undefined()): static|PrimitiveTypeAbstract
    {
        try {
            return static::fromBool($value);
        } catch (StringTypeException|Sha256StringTypeException) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromDecimal(string $value, PrimitiveTypeAbstract $default = new Undefined()): static|PrimitiveTypeAbstract
    {
        try {
            return static::fromDecimal($value);
        } catch (Sha256StringTypeException) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromFloat(float $value, PrimitiveTypeAbstract $default = new Undefined()): static|PrimitiveTypeAbstract
    {
        try {
            return static::fromFloat($value);
        } catch (FloatTypeException|Sha256StringTypeException|StringTypeException) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromInt(int $value, PrimitiveTypeAbstract $default = new Undefined()): static|PrimitiveTypeAbstract
    {
        try {
            return static::fromInt($value);
        } catch (Sha256StringTypeException) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromMixed(mixed $value, PrimitiveTypeAbstract $default = new Undefined()): static|PrimitiveTypeAbstract
    {
        if (is_scalar($value) || $value instanceof Stringable) {
            try {
                return static::fromString((string) $value);
            } catch (Sha256StringTypeException) {
                return $default;
            }
        }

        return $default;
    }

    /**
     * @psalm-pure
     */
    public static function tryFromString(string $value, PrimitiveTypeAbstract $default = new Undefined()): static|PrimitiveTypeAbstract
    {
        try {
            return static::fromString($value);
        } catch (Sha256StringTypeException) {
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
