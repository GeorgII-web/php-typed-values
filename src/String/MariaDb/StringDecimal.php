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
 * MariaDB DECIMAL value encoded as a string.
 *
 * Accepts canonical decimal strings like "123", "-5", or "3.14". No leading
 * plus sign and no invalid forms like ".5" or "1." are allowed. The original
 * string is preserved as provided.
 *
 * Example
 *  - $d = StringDecimal::fromString('3.14');
 *    $d->toString(); // '3.14'
 *  - StringDecimal::fromString('abc'); // throws DecimalStringTypeException
 *
 * Note: Use toFloat() only when the decimal can be represented exactly by a
 * PHP float. The method verifies an exact round‑trip: (string)(float)$src must
 * equal the original string and throws otherwise.
 *
 * @psalm-immutable
 */
class StringDecimal extends StrType
{
    /**
     * @readonly
     */
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
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static($value);
    }

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value)
    {
        try {
            return static::fromString($value);
        } catch (TypeException $exception) {
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
