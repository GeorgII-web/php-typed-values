<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Array;

use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Contract for array typed values.
 *
 * @psalm-immutable
 */
interface ArrayTypeInterface
{
    public function value(): array;

    public static function fromArray(array $value): static;

    public static function tryFromArray(array $value): static|Undefined;
}
