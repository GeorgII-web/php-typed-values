<?php

declare(strict_types=1);

namespace PhpTypedValues\Bool;

use Exception;
use PhpTypedValues\Base\Primitive\Bool\BoolType;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\BoolTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_int;
use function is_string;
use function sprintf;
use function strtolower;
use function trim;

/**
 * Literal boolean true typed value.
 *
 * Accepts common true-like representations in factories:
 *  - Strings: "true", "1", "yes", "on", "y" (case-insensitive)
 *  - Ints: 1
 *
 * Example
 *  - $t = TrueStandard::fromString('YES');
 *    $t->value(); // true
 *  - $t = TrueStandard::fromInt(1);
 *    (string) $t; // "true"
 *
 * @psalm-immutable
 */
readonly class TrueStandard extends BoolType
{
    /**
     * @var true
     */
    protected bool $value;

    /**
     * @throws BoolTypeException
     */
    public function __construct(bool $value)
    {
        if ($value !== true) {
            throw new BoolTypeException('Expected true literal, got "false"');
        }

        $this->value = true;
    }

    /**
     * @throws BoolTypeException
     */
    public static function fromString(string $value): static
    {
        $v = strtolower(trim($value));
        if ($v === 'true' || $v === '1' || $v === 'yes' || $v === 'on' || $v === 'y') {
            return new static(true);
        }

        throw new BoolTypeException(sprintf('Expected string representing true, got "%s"', $value));
    }

    /**
     * @throws BoolTypeException
     */
    public static function fromInt(int $value): static
    {
        if ($value === 1) {
            return new static(true);
        }

        throw new BoolTypeException(sprintf('Expected int "1" for true, got "%s"', $value));
    }

    public function value(): true
    {
        return $this->value;
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromInt(
        int $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType {
        try {
            /** @var static */
            return static::fromInt($value);
        } catch (Exception) {
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
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType {
        try {
            /** @var static */
            return match (true) {
                is_bool($value) => static::fromBool($value), // Boolean true\false
                is_int($value) => static::fromInt($value), // Integer 1\0
                $value === 1.0 => static::fromInt((int) $value), // Floats 1.0
                //                ($value instanceof self) => static::fromBool($value->value()), // BoolType Class - toString() will care about this case
                is_string($value) || $value instanceof Stringable => static::fromString((string) $value), // String "true","1","yes", etc.
                default => throw new TypeException('Value cannot be cast to boolean'),
            };
        } catch (Exception) {
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
     */
    public static function tryFromString(
        string $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    public function toString(): string
    {
        return 'true';
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    public function jsonSerialize(): true
    {
        return $this->value();
    }

    /**
     * @throws BoolTypeException
     */
    public static function fromBool(bool $value): static
    {
        return new static($value);
    }
}
