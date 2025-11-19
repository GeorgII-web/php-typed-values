<?php

declare(strict_types=1);

namespace PhpTypedValues\Code\Float;

use PhpTypedValues\Code\Assert\Assert;
use PhpTypedValues\Code\Exception\NumericTypeException;

/**
 * @psalm-immutable
 */
abstract readonly class FloatType implements FloatTypeInterface
{
    /**
     * @throws NumericTypeException
     */
    protected static function assertNumericString(string $value): void
    {
        Assert::numeric($value, 'String has no valid float');
    }

    public function toString(): string
    {
        return (string) $this->value();
    }
}
