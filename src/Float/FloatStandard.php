<?php

declare(strict_types=1);

namespace PhpTypedValues\Float;

use Exception;
use PhpTypedValues\Base\Primitive\Float\FloatTypeAbstractAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;

/**
 * Generic float-typed value.
 *
 * Wraps any PHP float (double) and provides factories from native float or
 * validated string, along with convenient string formatting.
 *
 * Example
 *  - $v = FloatStandard::fromString('3.14');
 *    $v->value(); // 3.14 (float)
 *  - $v = FloatStandard::fromFloat(0.5);
 *    (string) $v; // "0.5"
 *
 * @psalm-immutable
 */
readonly class FloatStandard extends FloatTypeAbstractAbstract
{
    protected float $value;

    /**
     * @throws FloatTypeException
     */
    public function __construct(float $value)
    {
        if (is_infinite($value)) {
            throw new FloatTypeException('Infinite float value');
        }

        if (is_nan($value)) {
            throw new FloatTypeException('Not a number float value');
        }

        $this->value = $value;
    }

    /**
     * @throws FloatTypeException
     */
    public static function fromFloat(float $value): static
    {
        return new static($value);
    }

    /**
     * String conversion uses a setting called serialize_precision
     * (usually 14 or 17, but often configured to round the last digit
     * for "cleaner" output).
     *
     * @throws FloatTypeException
     */
    public static function fromString(string $value): static
    {
        return new static(parent::getFloatFromString($value));
    }

    public function value(): float
    {
        return $this->value;
    }

    public function jsonSerialize(): float
    {
        return $this->value;
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

    public function toString(): string
    {
        return (string) $this->value;
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
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
    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return match (true) {
                is_float($value), is_int($value) => static::fromFloat($value),
                ($value instanceof self) => static::fromFloat($value->value()),
                is_bool($value) => static::fromFloat($value ? 1.0 : 0.0),
                is_string($value) || $value instanceof Stringable => static::fromString((string) $value),
                default => throw new TypeException('Value cannot be cast to float'),
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
}
