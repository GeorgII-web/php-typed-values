<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Float;

use PhpTypedValues\Undefined\Alias\Undefined;

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

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value);

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromFloat(float $value);

    public function __toString(): string;
}
