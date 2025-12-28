<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\ArrayType;

use PhpTypedValues\ArrayType\ArrayUndefined;
use PhpTypedValues\Exception\TypeException;

/**
 * Base implementation for array typed values.
 *
 * Provides an immutable, iterable, countable, and JSON‑serializable
 * collection of typed items. Concrete implementations define item
 * validation and factory behavior.
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @template TItem
 *
 * @template-implements ArrayTypeInterface<TItem>
 *
 * @psalm-immutable
 */
abstract readonly class ArrayType implements ArrayTypeInterface
{
    /**
     * @template T of ArrayTypeInterface
     *
     * @param list<mixed> $value
     * @param T           $default
     *
     * @return static|T
     *
     * @psalm-return ($default is ArrayUndefined ? static : static|T)
     */
    public static function tryFromArray(
        array $value,
        ArrayTypeInterface $default = new ArrayUndefined(),
    ): static|ArrayTypeInterface {
        try {
            /** @var static */
            return static::fromArray($value);
        } catch (TypeException) {
            /* @var T $default */
            return $default;
        }
    }
}
