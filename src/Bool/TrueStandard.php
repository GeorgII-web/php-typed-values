<?php

declare(strict_types=1);

namespace PhpTypedValues\Bool;

use PhpTypedValues\Exception\BoolTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Internal\Primitive\Bool\BoolType;
use PhpTypedValues\Undefined\Alias\Undefined;

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
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     * @param mixed $value
     */
    public static function tryFromMixed($value)
    {
        try {
            return static::fromString(
                static::convertMixedToString($value)
            );
        } catch (TypeException $exception) {
            return Undefined::create();
        }
    }

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value)
    {
        try {
            return static::fromString($value);
        } catch (TypeException $exception) {
            return Undefined::create();
        }
    }

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromInt(int $value)
    {
        try {
            return static::fromInt($value);
        } catch (TypeException $exception) {
            return Undefined::create();
        }
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

    public function value(): bool
    {
        return $this->value;
    }

    public function jsonSerialize(): bool
    {
        return $this->value();
    }

    public function toString(): string
    {
        return $this->value() ? 'true' : 'false';
    }

    /**
     * @throws BoolTypeException
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static($value);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function isEmpty(): bool
    {
        return false;
    }
}
