<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Integer;

use const FILTER_VALIDATE_INT;

use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Exception\ReasonableRangeIntegerTypeException;
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

    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @throws IntegerTypeException
     * @throws ReasonableRangeIntegerTypeException
     */
    protected static function getIntegerFromString(string $value): int
    {
        // First, check if filter_var even considers it an integer in range
        $filtered = filter_var($value, FILTER_VALIDATE_INT);
        if ($filtered === false) {
            // If it looks like a canonical decimal integer but filter_var failed, it's an overflow.
            // Regex matches: 0, -0 (canonical 0), or non-zero numbers without leading zeros.
            if (preg_match('/^-?(?:0|[1-9]\d*)$/', $value)) {
                throw new ReasonableRangeIntegerTypeException(sprintf('String "%s" has no reasonable range integer value', $value));
            }

            throw new IntegerTypeException(sprintf('String "%s" has no valid strict integer value', $value));
        }

        return $filtered;
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-return (static&IntType)|T
     */
    abstract public static function tryFromInt(
        int $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType;

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromString(
        string $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType;

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    abstract public static function tryFromMixed(
        mixed $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType;
}
