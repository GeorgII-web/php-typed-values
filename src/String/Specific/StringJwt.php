<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\String\JwtStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_scalar;
use function preg_match;
use function sprintf;

/**
 * JSON Web Token (JWT) string.
 *
 * Validates that the string follows the JWT format (three base64url encoded parts separated by dots).
 *
 * @psalm-immutable
 */
readonly class StringJwt extends StringTypeAbstract
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws JwtStringTypeException
     */
    public function __construct(string $value)
    {
        if ($value === '') {
            throw new JwtStringTypeException('Expected non-empty JWT');
        }

        if (!preg_match('/^[a-zA-Z0-9\-_]+\.[a-zA-Z0-9\-_]+\.[a-zA-Z0-9\-_]+$/', $value)) {
            throw new JwtStringTypeException(sprintf('Expected valid JWT, got "%s"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws StringTypeException
     * @throws JwtStringTypeException
     *
     * @psalm-pure
     */
    public static function fromBool(bool $value): static
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws JwtStringTypeException
     *
     * @psalm-pure
     */
    public static function fromDecimal(string $value): static
    {
        return new static(static::decimalToString($value));
    }

    /**
     * @throws JwtStringTypeException
     *
     * @psalm-pure
     */
    public static function fromFloat(float $value): static
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws JwtStringTypeException
     *
     * @psalm-pure
     */
    public static function fromInt(int $value): static
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws JwtStringTypeException
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
     * @throws \PhpTypedValues\Exception\Decimal\DecimalTypeException
     */
    public function toDecimal(): string
    {
        return static::stringToDecimal($this->value);
    }

    /**
     * @throws StringTypeException
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
    public static function tryFromBool(bool $value, PrimitiveTypeAbstract $default = new Undefined()): PrimitiveTypeAbstract|static
    {
        try {
            return static::fromBool($value);
        } catch (StringTypeException) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromDecimal(string $value, PrimitiveTypeAbstract $default = new Undefined()): PrimitiveTypeAbstract|static
    {
        try {
            return static::fromDecimal($value);
        } catch (JwtStringTypeException) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromFloat(float $value, PrimitiveTypeAbstract $default = new Undefined()): PrimitiveTypeAbstract|static
    {
        try {
            return static::fromFloat($value);
        } catch (JwtStringTypeException) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromInt(int $value, PrimitiveTypeAbstract $default = new Undefined()): PrimitiveTypeAbstract|static
    {
        try {
            return static::fromInt($value);
        } catch (JwtStringTypeException) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromMixed(mixed $value, PrimitiveTypeAbstract $default = new Undefined()): PrimitiveTypeAbstract|static
    {
        if (is_scalar($value) || $value instanceof Stringable) {
            try {
                return static::fromString((string) $value);
            } catch (JwtStringTypeException) {
                return $default;
            }
        }

        return $default;
    }

    /**
     * @psalm-pure
     */
    public static function tryFromString(string $value, PrimitiveTypeAbstract $default = new Undefined()): PrimitiveTypeAbstract|static
    {
        try {
            return static::fromString($value);
        } catch (JwtStringTypeException) {
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
