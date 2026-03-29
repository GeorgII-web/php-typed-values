<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\String\StringUsernameException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function preg_match;
use function sprintf;

/**
 * Validates that the string consists only of alphanumeric characters,
 * dots, underscores, and hyphens, and is between 3 and 30 characters long.
 *
 * Example
 *  - $u = StringUsername::fromString('john_doe');
 *    (string) $u; // "john_doe"
 *  - StringUsername::fromString('hi'); // throws StringUsernameException (too short)
 *
 * @psalm-immutable
 */
class StringUsername extends StringTypeAbstract
{
    /** @var non-empty-string
     * @readonly */
    protected string $value;

    /**
     * @throws StringUsernameException
     */
    public function __construct(string $value)
    {
        if (preg_match('/^[a-zA-Z0-9._-]{3,30}$/', $value) !== 1) {
            throw new StringUsernameException(sprintf('Expected valid username, got "%s"', $value));
        }

        /** @var non-empty-string $value */
        $this->value = $value;
    }

    /**
     * @throws StringUsernameException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws StringUsernameException
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
     * @throws StringUsernameException
     * @throws StringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromFloat(float $value)
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws StringUsernameException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws StringUsernameException
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
        return $this->toString();
    }

    /**
     * @throws StringTypeException
     */
    public function toBool(): bool
    {
        return static::stringToBool($this->value());
    }

    /**
     * @throws DecimalTypeException
     */
    public function toDecimal(): string
    {
        return static::stringToDecimal($this->value());
    }

    /**
     * @throws FloatTypeException
     */
    public function toFloat(): float
    {
        return static::stringToFloat($this->value());
    }

    /**
     * @throws StringTypeException
     */
    public function toInt(): int
    {
        return static::stringToInt($this->value());
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
        } catch (Exception $exception) {
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
        } catch (Exception $exception) {
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
        } catch (Exception $exception) {
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
        } catch (Exception $exception) {
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
        if ($value === null) {
            return static::tryFromString('null', $default);
        }

        switch (true) {
            case is_string($value):
                return static::tryFromString($value, $default);
            case is_int($value):
                return static::tryFromInt($value, $default);
            case is_float($value):
                return static::tryFromFloat($value, $default);
            case is_bool($value):
                return static::tryFromBool($value, $default);
            case is_object($value) && method_exists($value, '__toString'):
                return static::tryFromString((string) $value, $default);
            default:
                return $default;
        }
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
        } catch (Exception $exception) {
            return $default;
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
