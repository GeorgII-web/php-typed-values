<?php

declare(strict_types=1);

namespace PhpTypedValues\Bool;

use Exception;
use PhpTypedValues\Base\Primitive\Bool\BoolTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Bool\BoolTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
use function is_int;
use function is_string;

/**
 * Literal boolean false typed value.
 *
 * Accepts common false-like representations in factories:
 *  - String: "false" (case-insensitive)
 *  - Int: 0
 *  - Float: 0.0
 *
 * Example
 *  - $f = FalseStandard::fromString('false');
 *    $f->value(); // false
 *  - $f = FalseStandard::fromInt(0);
 *    $f->toString(); // "false"
 *
 * @psalm-immutable
 */
class FalseStandard extends BoolTypeAbstract
{
    /**
     * @readonly
     */
    protected false $value;

    /**
     * @throws BoolTypeException
     */
    public function __construct(bool $value)
    {
        if ($value !== false) {
            throw new BoolTypeException('Expected "false" literal, got "true"');
        }

        $this->value = false;
    }

    /**
     * @psalm-pure
     *
     * @throws BoolTypeException
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static($value);
    }

    /**
     * @psalm-pure
     *
     * @throws FloatTypeException
     * @throws BoolTypeException
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
     * @throws BoolTypeException
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static(static::intToBool($value));
    }

    /**
     * @psalm-pure
     *
     * @throws BoolTypeException
     * @throws IntegerTypeException
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

    /**
     * @return false
     */
    public function jsonSerialize(): bool
    {
        return $this->value();
    }

    public function toBool(): bool
    {
        return $this->value();
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
        try {
            /** @var static */
            return static::fromBool($value);
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
                    return static::fromString((string) $value);
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

    /**
     * @return false
     */
    public function value(): bool
    {
        return $this->value;
    }
}
