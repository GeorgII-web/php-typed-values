<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use Exception;
use PhpTypedValues\Base\Primitive\Integer\IntegerTypeAbstractAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function sprintf;

/**
 * IntegerStandard (Generic integer-typed value).
 *
 * Wraps any PHP integer and provides factories from a strictly validated
 * string or a native int, along with convenient string formatting.
 *
 * Example
 *  - $v = IntegerStandard::fromString('-10');
 *    $v->value(); // -10 (int)
 *  - $v = IntegerStandard::fromInt(42);
 *    (string) $v; // "42"
 *
 * @psalm-immutable
 */
readonly class IntegerStandard extends IntegerTypeAbstractAbstract
{
    protected int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function fromInt(int $value): static
    {
        return new static($value);
    }

    /**
     * @throws IntegerTypeException
     */
    public static function fromString(string $value): static
    {
        return new static(parent::getIntegerFromString($value));
    }

    /**
     * @throws IntegerTypeException
     */
    public static function fromFloat(float $value): static
    {
        return new static(parent::getIntegerFromFloat($value));
    }

    public static function fromBool(bool $value): static
    {
        return new static((int) $value);
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-return (static&IntegerTypeAbstractAbstract)|T
     */
    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        /** @var static&IntegerTypeAbstractAbstract */
        return static::fromInt($value);
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
    public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromBool($value);
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
                is_int($value) => static::fromInt($value),
                is_float($value) => static::fromFloat($value),
                is_bool($value) => static::fromBool($value),
                is_string($value) || $value instanceof Stringable => static::fromString((string) $value),
                default => throw new TypeException('Value cannot be cast to int'),
            };
        } catch (Exception) {
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

    public function value(): int
    {
        return $this->value;
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return (string) $this->value();
    }

    public function toInt(): int
    {
        return $this->value;
    }

    /**
     * Some big integers converted to a float that can't be converted to the same int back.
     *
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

    public function jsonSerialize(): int
    {
        return $this->value();
    }

    public function isEmpty(): false
    {
        return false;
    }

    public function isUndefined(): false
    {
        return false;
    }
}
