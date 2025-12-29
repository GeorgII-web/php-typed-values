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
 * Literal boolean false typed value.
 *
 * Accepts common false-like representations in factories:
 *  - Strings: "false", "0", "no", "off", "n" (case-insensitive)
 *  - Ints: 0
 *
 * Example
 *  - $f = FalseStandard::fromString('Off');
 *    $f->value(); // false
 *  - $f = FalseStandard::fromInt(0);
 *    $f->toString(); // "false"
 *
 * @psalm-immutable
 */
class FalseStandard extends BoolType
{
    /**
     * @readonly
     */
    protected false $value;

    /**
     * @throws BoolTypeException
     */
    public function __construct(bool $value)
    {
        if ($value !== false) {
            throw new BoolTypeException('Expected false literal, got "true"');
        }

        $this->value = false;
    }

    /**
     * @throws BoolTypeException
     * @return static
     */
    public static function fromString(string $value)
    {
        $v = strtolower(trim($value));
        if ($v === 'false' || $v === '0' || $v === 'no' || $v === 'off' || $v === 'n') {
            return new static(false);
        }

        throw new BoolTypeException(sprintf('Expected string representing false, got "%s"', $value));
    }

    /**
     * @throws BoolTypeException
     * @return static
     */
    public static function fromInt(int $value)
    {
        if ($value === 0) {
            return new static(false);
        }

        throw new BoolTypeException(sprintf('Expected int "0" for false, got "%s"', $value));
    }

    /**
     * @return false
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
                case $value === 0.0:
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
        return 'false';
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
     * @return false
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
