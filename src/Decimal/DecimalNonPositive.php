<?php

declare(strict_types=1);

namespace PhpTypedValues\Decimal;

use Exception;
use PhpTypedValues\Base\Primitive\Decimal\DecimalTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Decimal\NonPositiveDecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function preg_match;
use function sprintf;

/**
 * DECIMAL non-positive value encoded as a string.
 *
 * Accepts canonical decimal strings like "-123.0", "-5.0", or "0.0". No leading
 * plus sign and no invalid forms like ".5" or "1." are allowed. The original
 * string is preserved as provided.
 *
 * @psalm-immutable
 */
class DecimalNonPositive extends DecimalTypeAbstract
{
    /**
     * @var non-empty-string
     * @readonly
     */
    protected string $value;

    /**
     * @throws NonPositiveDecimalTypeException
     * @throws DecimalTypeException
     */
    public function __construct(string $value)
    {
        $decimal = self::stringToDecimal($value);

        if ($decimal[0] !== '-' && preg_match('/^0+(\.0+)?$/', $decimal) !== 1) {
            throw new NonPositiveDecimalTypeException(sprintf('Decimal "%s" is not a non-positive value', $value));
        }

        $this->value = $decimal;
    }

    /**
     * @throws NonPositiveDecimalTypeException
     * @throws DecimalTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static(static::boolToDecimal($value));
    }

    /**
     * @throws NonPositiveDecimalTypeException
     * @throws DecimalTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromDecimal(string $value)
    {
        return new static($value);
    }

    /**
     * @throws FloatTypeException
     * @throws StringTypeException
     * @throws NonPositiveDecimalTypeException
     * @throws DecimalTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromFloat(float $value)
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws NonPositiveDecimalTypeException
     * @throws DecimalTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static(static::intToDecimal($value));
    }

    /**
     * @throws NonPositiveDecimalTypeException
     * @return never
     * @param null $value
     */
    public static function fromNull($value)
    {
        throw new NonPositiveDecimalTypeException('Value cannot be null');
    }

    /**
     * @throws NonPositiveDecimalTypeException
     * @throws DecimalTypeException
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

    /**
     * @return non-empty-string
     */
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
     * @return non-empty-string
     */
    public function toDecimal(): string
    {
        return $this->value();
    }

    /**
     * @throws FloatTypeException
     * @throws StringTypeException
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

    /**
     * @throws NonPositiveDecimalTypeException
     * @return never
     */
    public function toNull()
    {
        throw new NonPositiveDecimalTypeException('Value cannot be null');
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
            return static::fromString($value);
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
                case is_string($value) || is_object($value) && method_exists($value, '__toString'):
                    return static::fromString((string) $value);
                case is_float($value):
                    return static::fromFloat($value);
                case is_int($value):
                    return static::fromInt($value);
                case is_bool($value):
                    return static::fromBool($value);
                default:
                    throw new TypeException('Value cannot be cast to string');
            }
        } catch (Exception $exception) {
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

    /**
     * @return non-empty-string
     */
    public function value(): string
    {
        return $this->value;
    }
}
