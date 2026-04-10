<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use const FILTER_VALIDATE_MAC;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\MacAddressStringException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function filter_var;
use function is_bool;
use function is_float;
use function is_int;
use function is_scalar;
use function is_string;
use function sprintf;

/**
 * String MAC Address typed value.
 *
 * Wraps a string and validates it as a valid MAC address.
 *
 * Example
 *  - $v = StringMacAddress::fromString('00:00:5e:00:53:01');
 *    $v->toString(); // "00:00:5e:00:53:01"
 *
 * @psalm-immutable
 */
readonly class StringMacAddress extends StringTypeAbstract
{
    protected string $value;

    /**
     * @throws MacAddressStringException
     */
    public function __construct(string $value)
    {
        if (filter_var($value, FILTER_VALIDATE_MAC) === false) {
            throw new MacAddressStringException(sprintf('Invalid MAC address: %s', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws MacAddressStringException
     *
     * @psalm-pure
     */
    public static function fromBool(bool $value): static
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws MacAddressStringException
     *
     * @psalm-pure
     */
    public static function fromDecimal(string $value): static
    {
        return new static(static::decimalToString($value));
    }

    /**
     * @throws FloatTypeException
     * @throws MacAddressStringException
     * @throws MacAddressStringException
     *
     * @psalm-pure
     */
    public static function fromFloat(float $value): static
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws MacAddressStringException
     *
     * @psalm-pure
     */
    public static function fromInt(int $value): static
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws MacAddressStringException
     */
    public static function fromNull(null $value): never
    {
        throw new MacAddressStringException('StringMacAddress type cannot be created from null');
    }

    /**
     * @throws MacAddressStringException
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
        return $this->value();
    }

    public function toBool(): bool
    {
        return static::stringToBool($this->value());
    }

    public function toDecimal(): string
    {
        return static::stringToDecimal($this->value());
    }

    public function toFloat(): float
    {
        return static::stringToFloat($this->value());
    }

    public function toInt(): int
    {
        return static::stringToInt($this->value());
    }

    /**
     * @throws MacAddressStringException
     */
    public static function toNull(): never
    {
        throw new MacAddressStringException('StringMacAddress type cannot be converted to null');
    }

    public function toString(): string
    {
        return $this->value();
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromBool($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromDecimal(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromDecimal($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromFloat($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromInt($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return match (true) {
                is_string($value) => static::fromString($value),
                is_float($value) => static::fromFloat($value),
                is_int($value) => static::fromInt($value),
                is_bool($value) => static::fromBool($value),
                $value instanceof Stringable, is_scalar($value) => static::fromString((string) $value),
                $value === null => static::fromString('null'),
                default => throw new TypeException('Exception'),
            };
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
