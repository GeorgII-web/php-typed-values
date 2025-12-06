<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Bool;

/**
 * @psalm-immutable
 */
interface BoolTypeInterface
{
    public function value(): bool;

    /**
     * @return static
     */
    public static function fromString(string $value);

    /**
     * @return static
     */
    public static function fromBool(bool $value);

    public function toString(): string;
}
