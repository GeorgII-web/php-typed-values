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
 * Example
 *  - $jwt = StringJwt::fromString('header.payload.signature'); // 'header.payload.signature'
 *  - StringJwt::fromString('not-a-jwt'); // throws JwtStringTypeException
 *
 * @psalm-immutable
 */
class StringJwt extends StringTypeAbstract
{
    /** @var non-empty-string
     * @readonly */
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
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws JwtStringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromDecimal(string $value)
    {
        return new static(static::decimalToString($value));
    }

    /**
     * @throws JwtStringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromFloat(float $value)
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws JwtStringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws JwtStringTypeException
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
     * @return \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract|static
     */
    public static function tryFromBool(bool $value, PrimitiveTypeAbstract $default = null)
    {
        $default ??= new Undefined();
        try {
            return static::fromBool($value);
        } catch (StringTypeException $exception) {
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
        } catch (JwtStringTypeException $exception) {
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
        } catch (JwtStringTypeException $exception) {
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
        } catch (JwtStringTypeException $exception) {
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
            } catch (JwtStringTypeException $exception) {
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
        } catch (JwtStringTypeException $exception) {
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
