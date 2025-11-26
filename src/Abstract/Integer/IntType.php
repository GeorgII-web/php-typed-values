<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Integer;

use PhpTypedValues\Exception\IntegerTypeException;

use function sprintf;

/**
 * @psalm-immutable
 */
abstract readonly class IntType implements IntTypeInterface
{
    /**
     * @throws IntegerTypeException
     */
    protected static function assertIntegerString(string $value): void
    {
        // Strict check, avoid unexpected string conversion
        $convertedValue = (string) ((int) $value);
        if ($value !== $convertedValue) {
            throw new IntegerTypeException(sprintf('String "%s" has no valid integer value', $value));
        }
    }

    public function toString(): string
    {
        return (string) $this->value();
    }
}
