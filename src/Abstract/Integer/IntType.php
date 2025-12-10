<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Integer;

use PhpTypedValues\Abstract\AbstractType;
use PhpTypedValues\Exception\IntegerTypeException;

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
abstract readonly class IntType extends AbstractType implements IntTypeInterface
{
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

    public function toString(): string
    {
        return (string) $this->value();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
