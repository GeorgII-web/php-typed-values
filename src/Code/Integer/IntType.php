<?php

declare(strict_types=1);

namespace PhpTypedValues\Code\Integer;

use PhpTypedValues\Code\Assert\Assert;
use PhpTypedValues\Code\Exception\NumericTypeException;

/**
 * @psalm-immutable
 */
abstract readonly class IntType implements IntTypeInterface
{
    /**
     * @throws NumericTypeException
     */
    protected static function assertNumericString(string $value): void
    {
        Assert::integer($value, 'String has no valid integer');
    }

    public function toString(): string
    {
        return (string) $this->value();
    }
}
