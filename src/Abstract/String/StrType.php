<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\String;

/**
 * @psalm-immutable
 */
abstract class StrType implements StrTypeInterface
{
    public function toString(): string
    {
        return $this->value();
    }
}
