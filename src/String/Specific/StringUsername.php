<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\UsernameStringException;
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
readonly class StringUsername extends StringTypeAbstract
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws UsernameStringException
     */
    public function __construct(string $value)
    {
        if (preg_match('/^[a-zA-Z0-9._-]{3,30}$/', $value) !== 1) {
            throw new UsernameStringException(sprintf('Expected valid username, got "%s"', $value));
        }

        /** @var non-empty-string $value */
        $this->value = $value;
    }

    /**
     * @throws UsernameStringException
     *
     * @psalm-pure
     */
    public static function fromBool(bool $value): static
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws UsernameStringException
     *
     * @psalm-pure
     */
    public static function fromDecimal(string $value): static
    {
        return new static(static::decimalToString($value));
    }

    /**
     * @throws FloatTypeException
     * @throws UsernameStringException
     * @throws UsernameStringException
     *
     * @psalm-pure
     */
    public static function fromFloat(float $value): static
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws UsernameStringException
     *
     * @psalm-pure
     */
    public static function fromInt(int $value): static
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws UsernameStringException
     */
    public static function fromNull(null $value): never
    {
        throw new UsernameStringException('StringUsername type cannot be created from null');
    }

    /**
     * @throws UsernameStringException
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
     * @throws UsernameStringException
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
     * @throws UsernameStringException
     */
    public function toInt(): int
    {
        return static::stringToInt($this->value());
    }

    /**
     * @throws UsernameStringException
     */
    public static function toNull(): never
    {
        throw new UsernameStringException('StringUsername type cannot be converted to null');
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
        } catch (Exception) {
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
        } catch (Exception) {
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
        } catch (Exception) {
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
        } catch (Exception) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromMixed(mixed $value, PrimitiveTypeAbstract $default = new Undefined()): PrimitiveTypeAbstract|static
    {
        if ($value === null) {
            return static::tryFromString('null', $default);
        }

        return match (true) {
            is_string($value) => static::tryFromString($value, $default),
            is_int($value) => static::tryFromInt($value, $default),
            is_float($value) => static::tryFromFloat($value, $default),
            is_bool($value) => static::tryFromBool($value, $default),
            $value instanceof Stringable => static::tryFromString((string) $value, $default),
            default => $default,
        };
    }

    /**
     * @psalm-pure
     */
    public static function tryFromString(string $value, PrimitiveTypeAbstract $default = new Undefined()): PrimitiveTypeAbstract|static
    {
        try {
            return static::fromString($value);
        } catch (Exception) {
            return $default;
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
