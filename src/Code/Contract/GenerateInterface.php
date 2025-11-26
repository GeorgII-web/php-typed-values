<?php

declare(strict_types=1);

namespace PhpTypedValues\Code\Contract;

/**
 * @psalm-immutable
 */
interface GenerateInterface
{
    public static function generate(): static;
}
