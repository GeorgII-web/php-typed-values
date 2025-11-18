<?php

declare(strict_types=1);

namespace GeorgiiWeb\PhpTypedValues\Types\Base;

use GeorgiiWeb\PhpTypedValues\Contracts\BaseTypeInterface;
use GeorgiiWeb\PhpTypedValues\Contracts\IntTypeInterface;
use GeorgiiWeb\PhpTypedValues\Exception\IntegerTypeException;

/**
 * @psalm-immutable
 */
abstract class BaseIntType implements BaseTypeInterface, IntTypeInterface
{
    protected readonly int $value;

    public function __construct(int $value)
    {
        $this->assert($value);
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    /**
     * @throws IntegerTypeException
     */
    public static function fromString(string $value): self
    {
        if (!preg_match('/^-?\d+$/', $value)) {
            throw new IntegerTypeException('String has no valid integer');
        }

        return new static((int) $value);
    }

    public static function fromInt(int $value): self
    {
        return new static($value);
    }

    public function toString(): string
    {
        return (string) $this->value;
    }
}
