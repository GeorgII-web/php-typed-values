<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Array;

use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Contract for array typed values.
 *
 * @template TItem
 *
 * @psalm-immutable
 */
interface ArrayTypeInterface
{
    /**
     * @psalm-return list<TItem>
     */
    public function value(): array;

    /**
     * @psalm-param list<mixed> $value
     */
    public static function fromArray(array $value): static;

    /**
     * @psalm-param list<mixed> $value
     */
    public static function tryFromArray(array $value): static|Undefined;
}
