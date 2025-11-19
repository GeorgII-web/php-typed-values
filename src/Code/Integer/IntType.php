<?php

declare(strict_types=1);

namespace PhpTypedValues\Code\Integer;

use PhpTypedValues\Code\Assert\Assert;
use PhpTypedValues\Code\Exception\TypeException;

/**
 * @psalm-immutable
 */
abstract readonly class IntType implements IntTypeInterface
{
    /**
     * @throws TypeException
     */
    protected static function assertNumericString(string $value): void
    {
        Assert::integerish($value, 'String has no valid integer');
    }

    public function toString(): string
    {
        return (string) $this->value();
    }
}
