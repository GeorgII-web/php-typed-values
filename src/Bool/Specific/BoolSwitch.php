<?php

declare(strict_types=1);

namespace PhpTypedValues\Bool\Specific;

use Exception;
use PhpTypedValues\Base\Primitive\Bool\BoolTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Bool\SwitchBoolTypeException;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
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
 * BoolSwitch (on / off).
 *
 * Wraps a native bool and provides factories from strings/ints with common
 * true/false synonyms, alongside strictly managing "on" and "off" as labels.
 *
 * @psalm-immutable
 */
class BoolSwitch extends BoolTypeAbstract
{
    /**
     * @readonly
     */
    protected bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    /**
     * @psalm-pure
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static($value);
    }

    /**
     * @psalm-pure
     *
     * @throws DecimalTypeException
     * @return static
     */
    public static function fromDecimal(string $value)
    {
        return new static(static::decimalToBool($value));
    }

    /**
     * @psalm-pure
     *
     * @throws FloatTypeException
     * @return static
     */
    public static function fromFloat(float $value)
    {
        return new static(static::floatToBool($value));
    }

    /**
     * @psalm-pure
     *
     * @throws IntegerTypeException
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static(static::intToBool($value));
    }

    /**
     * @psalm-pure
     *
     * @throws SwitchBoolTypeException
     * @return static
     */
    public static function fromLabel(string $label)
    {
        switch ($label) {
            case 'on':
                $value = true;
                break;
            case 'off':
                $value = false;
                break;
            default:
                throw new SwitchBoolTypeException(sprintf('Expected boolean switch label "on" or "off", got "%s"', $label));
        }

        return new static($value);
    }

    /**
     * @psalm-pure
     *
     * @throws StringTypeException
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static(static::stringToBool($value));
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

    public function jsonSerialize(): bool
    {
        return $this->value();
    }

    public function toBool(): bool
    {
        return $this->value();
    }

    /**
     * @return non-empty-string
     */
    public function toDecimal(): string
    {
        return static::boolToDecimal($this->value());
    }

    public function toFloat(): float
    {
        return static::boolToFloat($this->value());
    }

    public function toInt(): int
    {
        return static::boolToInt($this->value());
    }

    /**
     * @return non-empty-string
     */
    public function toLabel(): string
    {
        return $this->value() ? 'on' : 'off';
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return static::boolToString($this->value());
    }

    /**
     * @psalm-pure
     *
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
        /** @var static */
        return static::fromBool($value);
    }

    /**
     * @psalm-pure
     *
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
     * @psalm-pure
     *
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
     * @psalm-pure
     *
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromLabel(
        string $value,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromLabel($value);
        } catch (Exception $exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @psalm-pure
     *
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
                case is_bool($value):
                    return static::fromBool($value);
                case is_int($value):
                    return static::fromInt($value);
                case is_float($value):
                    return static::fromFloat($value);
                case $value instanceof self:
                    return static::fromBool($value->value());
                case is_string($value) || is_object($value) && method_exists($value, '__toString'):
                    return static::tryFromLabel((string) $value, static::tryFromString((string) $value));
                default:
                    throw new TypeException('Value cannot be cast to boolean');
            }
        } catch (Exception $exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @psalm-pure
     *
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

    public function value(): bool
    {
        return $this->value;
    }
}
