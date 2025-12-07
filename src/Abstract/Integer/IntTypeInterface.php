<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Integer;

/**
 * @psalm-immutable
 */
interface IntTypeInterface
{
    public function value(): int;

    /**
     * @return static
     */
    public static function fromInt(int $value);

    public function toString(): string;

    /**
     * @return static
     */
    public static function fromString(string $value);

    public function __toString(): string;
}
