<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Float;

use PhpTypedValues\Abstract\AbstractType;
use PhpTypedValues\Exception\FloatTypeException;

use function sprintf;

/**
 * Base implementation for float-typed values.
 *
 * Provides common validation for float strings and formatting helpers for
 * value objects backed by float primitives.
 *
 * Example
 *  - $v = MyFloat::fromString('3.14');
 *  - $v->value(); // 3.14 (float)
 *  - (string) $v; // "3.14"
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract readonly class FloatType extends AbstractType implements FloatTypeInterface
{
    /**
     * @throws FloatTypeException
     */
    protected static function assertFloatString(string $value): void
    {
        if (!is_numeric($value)) {
            throw new FloatTypeException(sprintf('String "%s" has no valid float value', $value));
        }
    }
}
