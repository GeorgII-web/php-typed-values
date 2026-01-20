<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Bool\BoolTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\UuidStringTypeException;
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
use function strtolower;

/**
 * UUID version 7 (time‑ordered, Unix time‑based) string.
 *
 * Accepts an RFC 4122 UUID v7 in canonical dashed form. Input is treated
 * case‑insensitively and normalized to lowercase for consistent storage and
 * comparison.
 *
 * Example
 *  - $u = StringUuidV7::fromString('01890F2A-5BCD-7DEF-9ABC-1234567890AB');
 *    $u->toString(); // '01890f2a-5bcd-7def-9abc-1234567890ab'
 *  - StringUuidV7::fromString('not-a-uuid'); // throws UuidStringTypeException
 *
 * @psalm-immutable
 */
class StringUuidV7 extends StringTypeAbstract
{
    /** @var non-empty-string
     * @readonly */
    protected string $value;

    /**
     * @throws UuidStringTypeException
     */
    public function __construct(string $value)
    {
        // Normalize to lowercase for consistent representation
        $normalized = strtolower($value);

        if ($normalized === '') {
            // Distinct message for empty input
            throw new UuidStringTypeException(sprintf('Expected non-empty UUID v7 (xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx), got "%s"', $value));
        }

        // RFC 4122-style UUID v7:
        // xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx (hex, case-insensitive)
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $normalized) !== 1) {
            throw new UuidStringTypeException(sprintf('Expected UUID v7 (xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx), got "%s"', $value));
        }

        $this->value = $normalized;
    }

    /**
     * @throws UuidStringTypeException
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws FloatTypeException
     * @throws UuidStringTypeException
     * @return static
     */
    public static function fromFloat(float $value)
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws FloatTypeException
     * @throws UuidStringTypeException
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws UuidStringTypeException
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

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * @throws BoolTypeException
     */
    public function toBool(): bool
    {
        return static::stringToBool($this->value());
    }

    /**
     * @throws FloatTypeException
     */
    public function toFloat(): float
    {
        return static::stringToFloat($this->value());
    }

    /**
     * @throws IntegerTypeException
     */
    public function toInt(): int
    {
        return static::stringToInt($this->value());
    }

    /** @return non-empty-string */
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

    /** @return non-empty-string */
    public function value(): string
    {
        return $this->value;
    }
}
