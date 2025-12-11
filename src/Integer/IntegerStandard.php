<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use PhpTypedValues\Abstract\Integer\IntType;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Exception\TypeException;
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
class IntegerStandard extends IntType
{
    /**
     * @readonly
     */
    protected int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
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
        // IntegerStandard accepts any PHP int, so construction cannot fail.
        return new static($value);
    }

    /**
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static($value);
    }

    /**
     * @throws IntegerTypeException
     * @return static
     */
    public static function fromString(string $value)
    {
        parent::assertIntegerString($value);

        return new static((int) $value);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function jsonSerialize(): int
    {
        return $this->value();
    }
}
