<?php

declare(strict_types=1);

namespace PhpTypedValues\BaseType;

use Override;
use PhpTypedValues\Contract\BaseTypeInterface;
use PhpTypedValues\Contract\IntTypeInterface;
use PhpTypedValues\Exception\IntegerTypeException;

/**
 * @psalm-immutable
 */
abstract readonly class BaseIntType implements BaseTypeInterface, IntTypeInterface
{
    protected int $value;

    public function __construct(int $value)
    {
        $this->assert($value);
        $this->value = $value;
    }

    #[Override]
    public function value(): int
    {
        return $this->value;
    }

    /**
     * @throws IntegerTypeException
     */
    #[Override]
    public static function fromString(string $value): self
    {
        if (!preg_match('/^-?\d+$/', $value)) {
            throw new IntegerTypeException('String has no valid integer');
        }

        return new static((int) $value);
    }

    #[Override]
    public static function fromInt(int $value): self
    {
        return new static($value);
    }

    #[Override]
    public function toString(): string
    {
        return (string) $this->value;
    }
}
