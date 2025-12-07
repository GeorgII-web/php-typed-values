<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Bool;

use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * @psalm-immutable
 */
interface BoolTypeInterface
{
    public function value(): bool;

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value);

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromInt(int $value);

    /**
     * @return static
     */
    public static function fromString(string $value);

    /**
     * @return static
     */
    public static function fromBool(bool $value);

    public function toString(): string;

    public function __toString(): string;
}
