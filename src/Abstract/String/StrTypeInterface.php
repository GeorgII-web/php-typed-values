<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\String;

/**
 * @psalm-immutable
 */
interface StrTypeInterface
{
    public function value(): string;

    /**
     * @return static
     */
    public static function fromString(string $value);

    public function toString(): string;
}
