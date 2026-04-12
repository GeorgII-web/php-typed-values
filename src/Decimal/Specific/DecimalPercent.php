<?php

declare(strict_types=1);

namespace PhpTypedValues\Decimal\Specific;

use Exception;
use PhpTypedValues\Base\Primitive\Decimal\DecimalTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Decimal\PercentDecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function sprintf;

/**
 * DECIMAL percent value (0.0 to 100.0) encoded as a string.
 *
 * @psalm-immutable
 */
class DecimalPercent extends DecimalTypeAbstract
{
    /**
     * @var non-empty-string
     * @readonly
     */
    protected string $value;

    /**
     * @throws PercentDecimalTypeException
     * @throws DecimalTypeException
     */
    public function __construct(string $value)
    {
        if (!$this->isValidRange($value, 0, 100)) {
            throw new PercentDecimalTypeException(sprintf('Decimal "%s" is not a valid percent (0.0-100.0)', $value));
        }

        /**
         * @var non-empty-string $value
         */
        $this->value = $value;
    }

    /**
     * @throws PercentDecimalTypeException
     * @throws DecimalTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromBool(bool $value): self
    {
        return new static(static::boolToDecimal($value));
    }

    /**
     * @throws PercentDecimalTypeException
     * @throws DecimalTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromDecimal(string $value): self
    {
        return new static($value);
    }

    /**
     * @throws FloatTypeException
     * @throws StringTypeException
     * @throws PercentDecimalTypeException
     * @throws DecimalTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromFloat(float $value): self
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws PercentDecimalTypeException
     * @throws DecimalTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromInt(int $value): self
    {
        return new static(static::intToDecimal($value));
    }

    /**
     * @throws PercentDecimalTypeException
     * @return never
     */
    public static function fromNull(null $value)
    {
        throw new PercentDecimalTypeException('Value cannot be null');
    }

    /**
     * @throws PercentDecimalTypeException
     * @throws DecimalTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromString(string $value): self
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
     * @throws PercentDecimalTypeException
     * @return never
     */
    public function toNull()
    {
        throw new PercentDecimalTypeException('Value cannot be null');
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
    ): \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract {
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
    ): \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract {
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
    ): \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract {
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
    ): \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract {
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
    ): \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract {
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
    ): \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract {
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
