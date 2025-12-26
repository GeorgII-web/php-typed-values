<?php

declare(strict_types=1);

namespace PhpTypedValues\Bool;

use PhpTypedValues\Base\Primitive\Bool\BoolType;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\BoolTypeException;
use PhpTypedValues\Exception\TypeException;
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
readonly class TrueStandard extends BoolType
{
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

    public static function tryFromMixed(
        mixed $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType {
        try {
            return static::fromString(
                static::convertMixedToString($value)
            );
        } catch (TypeException) {
            return $default;
        }
    }

    public static function tryFromString(
        string $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType {
        try {
            return static::fromString($value);
        } catch (TypeException) {
            return $default;
        }
    }

    public static function tryFromInt(int $value): static|Undefined
    {
        try {
            return static::fromInt($value);
        } catch (TypeException) {
            return Undefined::create();
        }
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
     */
    public static function fromBool(bool $value): static
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
