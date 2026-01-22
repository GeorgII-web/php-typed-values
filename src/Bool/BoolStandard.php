<?php

declare(strict_types=1);

namespace PhpTypedValues\Bool;

use Exception;
use PhpTypedValues\Base\Primitive\Bool\BoolTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
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
 * Generic boolean typed value.
 *
 * Wraps a native bool and provides factories from strings/ints with common
 * true/false synonyms (case-insensitive) and convenient formatting helpers.
 *
 * Example
 *  - $v = BoolStandard::fromString('true');
 *    $v->value(); // true
 *  - $v = BoolStandard::fromInt(0);
 *    $v->toString(); // "false"
 *
 * @psalm-immutable
 */
readonly class BoolStandard extends BoolTypeAbstract
{
    protected bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public static function fromBool(bool $value): static
    {
        return new static($value);
    }

    /**
     * @throws FloatTypeException
     */
    public static function fromFloat(float $value): static
    {
        return new static(static::floatToBool($value));
    }

    /**
     * @throws IntegerTypeException
     */
    public static function fromInt(int $value): static
    {
        return new static(static::intToBool($value));
    }

    /**
     * @throws IntegerTypeException
     */
    public static function fromString(string $value): static
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
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        /** @var static */
        return static::fromBool($value);
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
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
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
     */
    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
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
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return match (true) {
                is_bool($value) => static::fromBool($value),
                is_int($value) => static::fromInt($value),
                is_float($value) => static::fromFloat($value),
                ($value instanceof self) => static::fromBool($value->value()),
                is_string($value) || $value instanceof Stringable => static::fromString((string) $value),
                default => throw new TypeException('Value cannot be cast to boolean'),
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
     */
    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    public function value(): bool
    {
        return $this->value;
    }
}
