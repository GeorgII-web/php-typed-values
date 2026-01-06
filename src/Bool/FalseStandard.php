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
readonly class FalseStandard extends BoolTypeAbstract
{
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
     * @throws BoolTypeException
     */
    public static function fromBool(bool $value): static
    {
        return new static($value);
    }

    /**
     * @throws FloatTypeException
     * @throws BoolTypeException
     */
    public static function fromFloat(float $value): static
    {
        return new static(static::floatToBool($value));
    }

    /**
     * @throws IntegerTypeException
     * @throws BoolTypeException
     */
    public static function fromInt(int $value): static
    {
        return new static(static::intToBool($value));
    }

    /**
     * @throws BoolTypeException
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

    public function jsonSerialize(): false
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

    public function value(): false
    {
        return $this->value;
    }
}
