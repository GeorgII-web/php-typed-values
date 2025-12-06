<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\MariaDb;

use PhpTypedValues\Abstract\Integer\IntType;
use PhpTypedValues\Exception\IntegerTypeException;

use function sprintf;

/**
 * Database tiny integer (TINYINT signed: -128..127).
 *
 * Example "-5"
 *
 * @psalm-immutable
 */
class IntTiny extends IntType
{
    /** @var int<-128, 127>
     * @readonly */
    protected int $value;

    /**
     * @throws IntegerTypeException
     */
    public function __construct(int $value)
    {
        if ($value < -128 || $value > 127) {
            throw new IntegerTypeException(sprintf('Expected tiny integer in range -128..127, got "%d"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws IntegerTypeException
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

    /**
     * @return int<-128, 127>
     */
    public function value(): int
    {
        return $this->value;
    }
}
