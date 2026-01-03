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
class BoolStandard extends BoolType
{
    /**
     * @readonly
     */
    protected bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    /**
     * @throws BoolTypeException
     * @return static
     */
    public static function fromString(string $value)
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
     * @return static
     */
    public static function fromInt(int $value)
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
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromInt(
        int $value,
        PrimitiveType $default = null
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
                case is_bool($value):
                    return static::fromBool($value);
                case is_int($value):
                    return static::fromInt($value);
                case $value === 0.0 || $value === 1.0:
                    return static::fromInt((int) $value);
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

    /**
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static($value);
    }
}
