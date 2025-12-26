<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Integer;

use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

use function sprintf;

/**
 * Base implementation for integer-typed values.
 *
 * Contains strict string-to-int validation and common formatting helpers
 * for value objects backed by integer primitives.
 *
 * Example
 *  - $v = MyInt::fromString('42');
 *  - $v->value(); // 42 (int)
 *  - (string) $v; // "42"
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract readonly class IntType extends PrimitiveType implements IntTypeInterface
{
    abstract public function value(): int;

    /**
     * @throws IntegerTypeException
     */
    protected static function assertIntegerString(string $value): void
    {
        // Strict check, avoid unexpected string conversion
        $convertedValue = (string) ((int) $value);
        if ($value !== $convertedValue) {
            throw new IntegerTypeException(sprintf('String "%s" has no valid strict integer value', $value));
        }
    }

    abstract public static function tryFromString(
        string $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType|Undefined;

    abstract public static function tryFromMixed(
        mixed $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType|Undefined;
}
