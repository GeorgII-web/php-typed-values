<?php

declare(strict_types=1);

namespace PhpTypedValues\String\MariaDb;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Base\Primitive\String\StrType;
use PhpTypedValues\Exception\DecimalStringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_scalar;
use function is_string;
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
     * @var non-empty-string
     * @readonly
     */
    protected string $value;

    /**
     * @throws DecimalStringTypeException
     */
    public function __construct(string $value)
    {
        $this->value = self::getFromDecimalString($value);
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
     * @return non-empty-string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Convert to float only if the string representation exactly matches string-casted float.
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
     * @return non-empty-string
     *
     * @throws DecimalStringTypeException
     */
    private static function getFromDecimalString(string $value): string
    {
        if ($value === '') {
            throw new DecimalStringTypeException('Expected non-empty decimal string');
        }

        if (preg_match('/^-?\d+(?:\.\d+)?$/', $value) !== 1) {
            throw new DecimalStringTypeException(sprintf('Expected decimal string (e.g., "123", "-1", "3.14"), got "%s"', $value));
        }

        return $value;
    }

    /**
     * @return non-empty-string
     */
    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function isTypeOf(string ...$classNames): bool
    {
        foreach ($classNames as $className) {
            if ($this instanceof $className) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->value();
    }

    public function isEmpty(): bool
    {
        // Decimal values are never empty by construction; constructor rejects empty strings
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     * @param mixed $value
     */
    public static function tryFromMixed(
        $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        try {
            switch (true) {
                case is_string($value):
                    return static::fromString($value);
                case is_object($value) && method_exists($value, '__toString'):
                case is_scalar($value):
                    return static::fromString((string) $value);
                default:
                    throw new TypeException('Value cannot be cast to string');
            }
        } catch (Exception $exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception $exception) {
            /** @var T */
            return $default;
        }
    }
}
