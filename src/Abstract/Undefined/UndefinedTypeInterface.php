<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Undefined;

/**
 * @psalm-immutable
 */
interface UndefinedTypeInterface
{
    public static function create(): self;

    public function value(): void;

    public function toString(): void;
}
