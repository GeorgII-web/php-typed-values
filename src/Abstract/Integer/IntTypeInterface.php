<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Integer;

use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * @psalm-immutable
 */
interface IntTypeInterface
{
    public function value(): int;

    /**
     * @return static
     */
    public static function fromString(string $value);

    /**
     * @return static
     */
    public static function fromInt(int $value);

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value);

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromInt(int $value);

    public function toString(): string;

    public function __toString(): string;
}
