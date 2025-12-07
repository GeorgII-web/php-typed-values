<?php

declare(strict_types=1);

namespace PhpTypedValues\String\MariaDb;

use PhpTypedValues\Abstract\String\StrType;
use PhpTypedValues\Exception\DecimalStringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

use function preg_match;
use function sprintf;

/**
 * MariaDB DECIMAL value represented as a string.
 *
 * Example "123", "-5", "3.14"
 *
 * Note: Use toFloat() only when the decimal can be represented exactly by PHP float.
 *       The method verifies exact round-trip: (string)(float)$src must equal the original string.
 *
 * @psalm-immutable
 */
readonly class StringDecimal extends StrType
{
    protected string $value;

    /**
     * @throws DecimalStringTypeException
     */
    public function __construct(string $value)
    {
        self::assertDecimalString($value);
        $this->value = $value;
    }

    /**
     * @throws DecimalStringTypeException
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public static function tryFromString(string $value): static|Undefined
    {
        try {
            return static::fromString($value);
        } catch (TypeException) {
            return Undefined::create();
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    /**
     * Convert to float only if the string representation matches exactly string-casted float.
     *
     * @throws DecimalStringTypeException
     */
    public function toFloat(): float
    {
        $src = $this->value;
        $casted = (string) ((float) $src);
        if ($src !== $casted) {
            throw new DecimalStringTypeException(sprintf('Unexpected float conversion, source "%s" != casted "%s"', $src, $casted));
        }

        return (float) $src;
    }

    /**
     * Accepts optional leading minus, digits, and optional fractional part with at least one digit.
     * Disallows leading/trailing spaces, plus sign, and missing integer or fractional digits like ".5" or "1.".
     *
     * @throws DecimalStringTypeException
     */
    private static function assertDecimalString(string $value): void
    {
        if (preg_match('/^-?\d+(?:\.\d+)?$/', $value) !== 1) {
            throw new DecimalStringTypeException(sprintf('Expected decimal string (e.g., "123", "-1", "3.14"), got "%s"', $value));
        }
    }
}
