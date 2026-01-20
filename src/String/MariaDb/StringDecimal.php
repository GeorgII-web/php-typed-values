<?php

declare(strict_types=1);

namespace PhpTypedValues\String\MariaDb;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\DecimalStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
use function is_int;
use function is_scalar;
use function is_string;
use function preg_match;
use function sprintf;

/**
 * MariaDB DECIMAL value encoded as a string.
 *
 * Accepts canonical decimal strings like "123", "-5", or "3.14". No leading
 * plus sign and no invalid forms like ".5" or "1." are allowed. The original
 * string is preserved as provided.
 *
 * Example
 *  - $d = StringDecimal::fromString('3.14');
 *    $d->toString(); // '3.14'
 *  - StringDecimal::fromString('abc'); // throws DecimalStringTypeException
 *
 * Note: Use toFloat() only when the decimal can be represented exactly by a
 * PHP float. The method verifies an exact round‑trip: (string)(float)$src must
 * equal the original string and throws otherwise.
 *
 * @psalm-immutable
 */
class StringDecimal extends StringTypeAbstract
{
    /**
     * @var non-empty-string
     * @readonly
     */
    protected string $value;

    /**
     * @throws DecimalStringTypeException
     */
    public function __construct(string $value)
    {
        $this->value = self::getFromDecimalString($value);
    }

    /**
     * @throws DecimalStringTypeException
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws FloatTypeException
     * @throws DecimalStringTypeException
     * @return static
     */
    public static function fromFloat(float $value)
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws DecimalStringTypeException
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws DecimalStringTypeException
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

    /**
     * @return non-empty-string
     */
    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * @throws IntegerTypeException
     */
    public function toBool(): bool
    {
        return static::stringToBool($this->value());
    }

    /**
     * @throws DecimalStringTypeException
     */
    public function toFloat(): float
    {
        $src = $this->value;
        $casted = (string) ((float) $src);
        if ($src !== $casted) {
            throw new DecimalStringTypeException(sprintf('Unexpected float conversion, source "%s" != casted "%s"', $src, $casted));
        }

        return (float) $src;
    }

    /**
     * @throws StringTypeException
     */
    public function toInt(): int
    {
        return static::stringToInt($this->value());
    }

    /**
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

    /**
     * @return non-empty-string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Accepts optional leading minus, digits, and optional fractional part with at least one digit.
     * Disallows leading/trailing spaces, plus sign, and missing integer or fractional digits like ".5" or "1.".
     *
     * @return non-empty-string
     *
     * @throws DecimalStringTypeException
     */
    private static function getFromDecimalString(string $value): string
    {
        if ($value === '') {
            throw new DecimalStringTypeException('Expected non-empty decimal string');
        }

        if (preg_match('/^-?\d+(?:\.\d+)?$/', $value) !== 1) {
            throw new DecimalStringTypeException(sprintf('Expected decimal string (e.g., "123", "-1", "3.14"), got "%s"', $value));
        }

        return $value;
    }
}
