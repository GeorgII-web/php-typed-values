<?php

declare(strict_types=1);

namespace PhpTypedValues\BaseType;

use PhpTypedValues\Contract\IntTypeInterface;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 */
abstract readonly class BaseIntType implements IntTypeInterface
{
    protected static function assertNumericString(string $value): void
    {
        //        Assert::numeric($value, 'String has no valid integer');
        Assert::integerish($value, 'String has no valid integer');
    }

    public function toString(): string
    {
        return (string) $this->value();
    }
}
