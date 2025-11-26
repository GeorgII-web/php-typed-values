<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer;

use PhpTypedValues\Code\Exception\IntegerTypeException;
use PhpTypedValues\Code\Integer\IntType;

use function sprintf;

/**
 * Week day number between 1 and 7.
 *
 * Example "5"
 *
 * @psalm-immutable
 */
readonly class IntegerWeekDay extends IntType
{
    /** @var int<1, 7> */
    protected int $value;

    /**
     * @throws IntegerTypeException
     */
    public function __construct(int $value)
    {
        if ($value < 1) {
            throw new IntegerTypeException(sprintf('Expected value between 1-7, got "%d"', $value));
        }

        if ($value > 7) {
            throw new IntegerTypeException(sprintf('Expected value between 1-7, got "%d"', $value));
        }

        $this->value = $value;
    }

    /**
     * @return int<1, 7>
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * @throws IntegerTypeException
     */
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
}
