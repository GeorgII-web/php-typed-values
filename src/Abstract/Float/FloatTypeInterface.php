<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Float;

/**
 * @psalm-immutable
 */
interface FloatTypeInterface
{
    public function value(): float;

    /**
     * @return static
     */
    public static function fromFloat(float $value);

    public function toString(): string;

    /**
     * @return static
     */
    public static function fromString(string $value);
}
