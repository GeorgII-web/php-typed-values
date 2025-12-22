<?php

declare(strict_types=1);

namespace PhpTypedValues\Bool;

use PhpTypedValues\Exception\BoolTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Internal\Primitive\Bool\BoolType;
use PhpTypedValues\Undefined\Alias\Undefined;

use function sprintf;

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

    public function jsonSerialize(): bool
    {
        return $this->value();
    }

    public function toString(): string
    {
        return $this->value() ? 'true' : 'false';
    }

    /**
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

    public function isUndefined(): bool
    {
        return false;
    }
}
