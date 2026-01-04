<?php

declare(strict_types=1);

namespace PhpTypedValues\Bool;

use Exception;
use PhpTypedValues\Base\Primitive\Bool\BoolTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Bool\BoolTypeException;
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
 * Generic boolean typed value.
 *
 * Wraps a native bool and provides factories from strings/ints with common
 * true/false synonyms (case-insensitive) and convenient formatting helpers.
 *
 * Example
 *  - $v = BoolStandard::fromString('yes');
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

    /**
     * @throws BoolTypeException
     */
    public static function fromString(string $value): static
    {
        $lowerCaseValue = strtolower(trim($value));

        if ($lowerCaseValue === 'true' || $lowerCaseValue === '1' || $lowerCaseValue === 'yes' || $lowerCaseValue === 'on' || $lowerCaseValue === 'y') {
            $boolValue = true;
        } elseif ($lowerCaseValue === 'false' || $lowerCaseValue === '0' || $lowerCaseValue === 'no' || $lowerCaseValue === 'off' || $lowerCaseValue === 'n') {
            $boolValue = false;
        } else {
            throw new BoolTypeException(sprintf('Expected string "true" or "false", got "%s"', $value));
        }

        return new static($boolValue);
    }

    /**
     * @throws BoolTypeException
     */
    public static function fromInt(int $value): static
    {
        if ($value === 1) {
            $boolValue = true;
        } elseif ($value === 0) {
            $boolValue = false;
        } else {
            throw new BoolTypeException(sprintf('Expected int "1" or "0", got "%s"', $value));
        }

        return new static($boolValue);
    }

    public function value(): bool
    {
        return $this->value;
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
                is_bool($value) => static::fromBool($value), // Boolean true\false
                is_int($value) => static::fromInt($value), // Integer 1\0
                ($value === 0.0 || $value === 1.0) => static::fromInt((int) $value), // Floats 1.0\0.0
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
        return $this->value() ? 'true' : 'false';
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

    public function jsonSerialize(): bool
    {
        return $this->value();
    }

    public static function fromBool(bool $value): static
    {
        return new static($value);
    }
}
