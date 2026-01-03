<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use Exception;
use PhpTypedValues\Base\Primitive\Integer\IntType;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Exception\ReasonableRangeIntegerTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function sprintf;

/**
 * IntegerPositive (Positive integer > 0).
 *
 * Ensures the wrapped value is strictly greater than zero. Provides factories
 * from strictly validated string and native int, plus convenient formatting.
 *
 * Example
 *  - $v = IntegerPositive::fromString('1');
 *    $v->value(); // 1 (int)
 *  - $v = IntegerPositive::fromInt(5);
 *    (string) $v; // "5"
 *
 * @psalm-immutable
 */
class IntegerPositive extends IntType
{
    /** @var positive-int
     * @readonly */
    protected int $value;

    /**
     * @throws IntegerTypeException
     */
    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new IntegerTypeException(sprintf('Expected positive integer, got "%d"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws IntegerTypeException
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static($value);
    }

    /**
     * @throws IntegerTypeException
     * @throws ReasonableRangeIntegerTypeException
     * @return static
     */
    public static function fromFloat(float $value)
    {
        return new static(parent::getIntegerFromFloat($value));
    }

    /**
     * @throws IntegerTypeException
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static((int) $value);
    }

    /**
     * @throws IntegerTypeException
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static(parent::getIntegerFromString($value));
    }

    /**
     * @return positive-int
     */
    public function toInt(): int
    {
        return $this->value;
    }

    /**
     * @return positive-int
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-return (static&IntType)|T
     */
    public static function tryFromInt(
        int $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static&IntType */
            return static::fromInt($value);
        } catch (TypeException $exception) {
            /* @var T $default */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        PrimitiveType $default = null
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
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     * @param mixed $value
     */
    public static function tryFromMixed(
        $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        try {
            switch (true) {
                case is_int($value):
                    return static::fromInt($value);
                case is_float($value):
                    return static::fromFloat($value);
                case is_bool($value):
                    return static::fromBool($value);
                case is_string($value) || is_object($value) && method_exists($value, '__toString'):
                    return static::fromString((string) $value);
                default:
                    throw new TypeException('Value cannot be cast to int');
            }
        } catch (Exception $exception) {
            /** @var T */
            return $default;
        }
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

    /**
     * @return positive-int
     */
    public function jsonSerialize(): int
    {
        return $this->value();
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return (string) $this->value();
    }

    /**
     * @throws IntegerTypeException
     */
    public function toFloat(): float
    {
        $toFloatValue = (float) $this->value;

        if ($this->value !== (int) $toFloatValue) {
            throw new IntegerTypeException(sprintf('Integer %s cannot be converted to float without losing precision', $this->value));
        }

        return $toFloatValue;
    }

    public function toBool(): bool
    {
        return (bool) $this->value();
    }

    /**
     * @return false
     */
    public function isEmpty(): bool
    {
        return false;
    }

    /**
     * @return false
     */
    public function isUndefined(): bool
    {
        return false;
    }
}
