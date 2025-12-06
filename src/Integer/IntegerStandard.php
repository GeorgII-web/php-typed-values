<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use PhpTypedValues\Abstract\Integer\IntType;
use PhpTypedValues\Exception\IntegerTypeException;

/**
 * Represents any PHP integer.
 *
 * Example "-10"
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
}
