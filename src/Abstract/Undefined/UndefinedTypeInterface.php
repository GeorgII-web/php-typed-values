<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Undefined;

/**
 * Contract for the special Undefined typed value.
 *
 * Represents an explicit absence of a meaningful value.
 * Methods that would normally expose a value or its string representation
 * intentionally throw to prevent accidental usage.
 *
 * Example
 *  - $u = Undefined::create();
 *  - $u->value(); // throws UndefinedTypeException
 *
 * @psalm-immutable
 */
interface UndefinedTypeInterface
{
    /**
     * @return static
     */
    public static function create();

    /**
     * @return never
     */
    public function value();

    /**
     * @return never
     */
    public function toInt();

    /**
     * @return never
     */
    public function toFloat();
}
