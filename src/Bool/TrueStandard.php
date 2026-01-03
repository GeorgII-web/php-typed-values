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
class TrueStandard extends BoolType
{
    /**
     * @var true
     * @readonly
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
     * @return static
     */
    public static function fromString(string $value)
    {
        $v = strtolower(trim($value));
        if ($v === 'true' || $v === '1' || $v === 'yes' || $v === 'on' || $v === 'y') {
            return new static(true);
        }

        throw new BoolTypeException(sprintf('Expected string representing true, got "%s"', $value));
    }

    /**
     * @throws BoolTypeException
     * @return static
     */
    public static function fromInt(int $value)
    {
        if ($value === 1) {
            return new static(true);
        }

        throw new BoolTypeException(sprintf('Expected int "1" for true, got "%s"', $value));
    }

    /**
     * @return true
     */
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
                case $value === 1.0:
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

    /**
     * @return true
     */
    public function jsonSerialize(): bool
    {
        return $this->value();
    }

    /**
     * @throws BoolTypeException
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static($value);
    }
}
