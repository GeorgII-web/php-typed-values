<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\String;

use PhpTypedValues\Abstract\TypeInterface;

/**
 * @psalm-immutable
 */
abstract class StrType implements TypeInterface, StrTypeInterface
{
    public function toString(): string
    {
        return $this->value();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
