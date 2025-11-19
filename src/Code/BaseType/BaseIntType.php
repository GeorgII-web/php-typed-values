<?php

declare(strict_types=1);

namespace PhpTypedValues\Code\BaseType;

use PhpTypedValues\Code\Assert\Assert;
use PhpTypedValues\Code\Contract\IntTypeInterface;
use PhpTypedValues\Code\Exception\TypeException;

/**
 * @psalm-immutable
 */
abstract readonly class BaseIntType implements IntTypeInterface
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
