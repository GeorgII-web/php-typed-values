<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Integer;

use const FILTER_VALIDATE_INT;
use const PHP_INT_MAX;
use const PHP_INT_MIN;

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
    public function __toString(): string
    {
        return $this->toString();
    }

    abstract public static function fromString(string $value): static;

    abstract public static function fromInt(int $value): static;

    abstract public static function fromFloat(float $value): static;

    abstract public static function fromBool(bool $value): static;

    /**
     * @throws ReasonableRangeIntegerTypeException
     * @throws IntegerTypeException
     */
    protected static function getIntegerFromFloat(float $value): int
    {
        if ($value > PHP_INT_MAX || $value < PHP_INT_MIN) {
            throw new ReasonableRangeIntegerTypeException(sprintf('Float %s is out of range for a system integer', $value));
        }

        $intValue = (int) $value;

        // Check if the float had a fractional part by comparing back
        if ((float) $intValue !== $value) {
            throw new IntegerTypeException(sprintf('Float %s cannot be converted to integer without losing precision', $value));
        }

        return $intValue;
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

        // Strict check, avoid unexpected string conversion
        $convertedValue = (string) $filtered;
        if ($value !== $convertedValue) {
            throw new IntegerTypeException(sprintf('String "%s" is not in canonical form ("%s")', $value, $filtered));
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

    abstract public function value(): int;

    abstract public function toInt(): int;

    abstract public function toFloat(): float;

    abstract public function toBool(): bool;

    abstract public function toString(): string;
}
