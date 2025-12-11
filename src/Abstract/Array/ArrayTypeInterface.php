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
     * @return static
     */
    public static function fromArray(array $value);

    /**
     * @psalm-param list<mixed> $value
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromArray(array $value);
}
