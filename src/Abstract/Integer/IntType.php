<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Integer;

use PhpTypedValues\Abstract\TypeInterface;
use PhpTypedValues\Exception\IntegerTypeException;

use function sprintf;

/**
 * @psalm-immutable
 */
abstract class IntType implements TypeInterface, IntTypeInterface
{
    /**
     * @throws IntegerTypeException
     */
    protected static function assertIntegerString(string $value): void
    {
        // Strict check, avoid unexpected string conversion
        $convertedValue = (string) ((int) $value);
        if ($value !== $convertedValue) {
            throw new IntegerTypeException(sprintf('String "%s" has no valid strict integer value', $value));
        }
    }

    public function toString(): string
    {
        return (string) $this->value();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
