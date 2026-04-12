<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\HexStringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function ctype_xdigit;
use function is_bool;
use function is_float;
use function is_int;
use function is_scalar;
use function is_string;
use function sprintf;

/**
 * Hexadecimal string.
 *
 * Validates that the string contains only valid hexadecimal characters (0-9, a-f, A-F).
 * The original value is preserved on successful validation.
 *
 * Example
 *  - $h = StringHex::fromString('4a6f686e');
 *    $h->value(); // '4a6f686e'
 *  - StringHex::fromString('not valid!'); // throws HexStringTypeException
 *
 * @psalm-immutable
 */
class StringHex extends StringTypeAbstract
{
    /** @var non-empty-string
     * @readonly */
    protected string $value;

    /**
     * @throws HexStringTypeException
     */
    public function __construct(string $value)
    {
        if (!ctype_xdigit($value)) {
            throw new HexStringTypeException(sprintf('Expected hexadecimal string, got "%s"', $value));
        }

        /** @var non-empty-string $value */
        $this->value = $value;
    }

    /**
     * @throws HexStringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromBool(bool $value): \PhpTypedValues\Base\Primitive\String\StringTypeAbstract
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws HexStringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromDecimal(string $value): \PhpTypedValues\Base\Primitive\String\StringTypeAbstract
    {
        return new static(static::decimalToString($value));
    }

    /**
     * @throws FloatTypeException
     * @throws HexStringTypeException
     * @throws HexStringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromFloat(float $value): \PhpTypedValues\Base\Primitive\String\StringTypeAbstract
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws HexStringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromInt(int $value): \PhpTypedValues\Base\Primitive\String\StringTypeAbstract
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws HexStringTypeException
     * @return never
     */
    public static function fromNull(null $value)
    {
        throw new HexStringTypeException('StringHex type cannot be created from null');
    }

    /**
     * Creates a Hex instance from an existing hexadecimal string.
     *
     * @throws HexStringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromString(string $value): \PhpTypedValues\Base\Primitive\String\StringTypeAbstract
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

    /**
     * @throws HexStringTypeException
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
     * @throws HexStringTypeException
     */
    public function toFloat(): float
    {
        return static::stringToFloat($this->value());
    }

    /**
     * @throws HexStringTypeException
     */
    public function toInt(): int
    {
        return static::stringToInt($this->value());
    }

    /**
     * @throws HexStringTypeException
     * @return never
     */
    public static function toNull()
    {
        throw new HexStringTypeException('StringHex type cannot be converted to null');
    }

    /**
     * Returns the hexadecimal string.
     *
     * @return non-empty-string
     */
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
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromBool($value);
        } catch (Exception $exception) {
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
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromDecimal($value);
        } catch (Exception $exception) {
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
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromFloat($value);
        } catch (Exception $exception) {
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
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromInt($value);
        } catch (Exception $exception) {
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
     * @param mixed $value
     */
    public static function tryFromMixed(
        $value,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            switch (true) {
                case is_string($value):
                    return static::fromString($value);
                case is_float($value):
                    return static::fromFloat($value);
                case is_int($value):
                    return static::fromInt($value);
                case is_bool($value):
                    return static::fromBool($value);
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
        PrimitiveTypeAbstract $default = null
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

    /** @return non-empty-string */
    public function value(): string
    {
        return $this->value;
    }
}
