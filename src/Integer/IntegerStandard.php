<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use PhpTypedValues\Base\Primitive\Integer\IntType;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Generic integer-typed value.
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
readonly class IntegerStandard extends IntType
{
    protected int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function tryFromInt(int $value): static|Undefined
    {
        return new static($value);
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
        parent::assertIntegerString($value);

        return new static((int) $value);
    }

    public function toInt(): int
    {
        return $this->value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function toString(): string
    {
        return (string) $this->value();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function jsonSerialize(): int
    {
        return $this->value();
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
