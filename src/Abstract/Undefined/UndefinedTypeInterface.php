<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Undefined;

/**
 * @psalm-immutable
 */
interface UndefinedTypeInterface
{
    /**
     * @return static
     */
    public static function create();

    public function value(): void;

    public function toString(): void;

    public function __toString(): string;
}
