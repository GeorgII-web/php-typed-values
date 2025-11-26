<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Float;

use PhpTypedValues\Exception\FloatTypeException;

use function sprintf;

/**
 * @psalm-immutable
 */
abstract readonly class FloatType implements FloatTypeInterface
{
    /**
     * @throws FloatTypeException
     */
    protected static function assertFloatString(string $value): void
    {
        if (!is_numeric($value)) {
            throw new FloatTypeException(sprintf('String "%s" has no valid float value', $value));
        }
    }

    public function toString(): string
    {
        return (string) $this->value();
    }
}
