<?php

declare(strict_types=1);

namespace PhpTypedValues\String\MariaDb;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\TinyTextStringException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
use function is_int;
use function is_scalar;
use function is_string;
use function mb_strlen;

/**
 * MariaDB TINYTEXT string.
 *
 * Accepts any string with length up to 255 characters (mb_strlen based).
 * The original string is preserved on success; longer values are rejected.
 *
 * Example
 *  - $v = StringTinyText::fromString('Hello world');
 *    $v->toString(); // 'Hello world'
 *  - StringTinyText::fromString(str_repeat('x', 256)); // throws StringTinyTextException
 *
 * @psalm-immutable
 */
class StringTinyText extends StringTypeAbstract
{
    /**
     * @readonly
     */
    protected string $value;

    /**
     * @throws TinyTextStringException
     */
    public function __construct(string $value)
    {
        if (mb_strlen($value) > 255) {
            throw new TinyTextStringException('String is too long, max 255 chars allowed');
        }

        $this->value = $value;
    }

    /**
     * @throws TinyTextStringException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromBool(bool $value): \PhpTypedValues\Base\Primitive\String\StringTypeAbstract
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws TinyTextStringException
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
     * @throws TinyTextStringException
     * @throws TinyTextStringException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromFloat(float $value): \PhpTypedValues\Base\Primitive\String\StringTypeAbstract
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws TinyTextStringException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromInt(int $value): \PhpTypedValues\Base\Primitive\String\StringTypeAbstract
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws TinyTextStringException
     * @return never
     */
    public static function fromNull(null $value)
    {
        throw new TinyTextStringException('StringTinyText type cannot be created from null');
    }

    /**
     * @throws TinyTextStringException
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
        return $this->value === '';
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
     * @throws TinyTextStringException
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
     * @throws TinyTextStringException
     */
    public function toFloat(): float
    {
        return static::stringToFloat($this->value());
    }

    /**
     * @throws TinyTextStringException
     */
    public function toInt(): int
    {
        return static::stringToInt($this->value());
    }

    /**
     * @throws TinyTextStringException
     * @return never
     */
    public static function toNull()
    {
        throw new TinyTextStringException('StringTinyText type cannot be converted to null');
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
                case null === $value:
                    return static::fromString('');
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

    public function value(): string
    {
        return $this->value;
    }
}
