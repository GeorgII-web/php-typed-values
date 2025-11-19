<?php

declare(strict_types=1);

namespace PhpTypedValues\Code\String;

/**
 * @psalm-immutable
 */
abstract readonly class StrType implements StrTypeInterface
{
    public function toString(): string
    {
        return $this->value();
    }
}
