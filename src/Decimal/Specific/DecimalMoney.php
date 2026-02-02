<?php

declare(strict_types=1);

namespace PhpTypedValues\Decimal\Specific;

use Exception;
use PhpTypedValues\Base\Primitive\Decimal\DecimalTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
use function is_int;
use function is_scalar;
use function is_string;
use function sprintf;

/**
 * DECIMAL money value encoded as a string.
 *
 * Accepts canonical decimal money strings with exactly 2 decimal places
 * like "123.45", "0.99", or "1000.00". Must be non-negative.
 * No leading plus sign and no invalid forms like ".5" or "1." are allowed.
 *
 * Example
 *  - $d = DecimalMoney::fromString('19.99');
 *    $d->toString(); // '19.99'
 *  - DecimalMoney::fromString('19.9'); // throws DecimalTypeException (not 2 decimals)
 *  - DecimalMoney::fromString('-5.00'); // throws DecimalTypeException (negative)
 *
 * Note: Use toFloat() only when the decimal can be represented exactly by a
 * PHP float. The method verifies an exact round‑trip cast, must
 * equal the original string, and throws otherwise.
 *
 * @psalm-immutable
 */
readonly class DecimalMoney extends DecimalTypeAbstract
{
    /**
     * @var non-empty-string
     */
    protected string $value;

    /**
     * @throws DecimalTypeException
     */
    public function __construct(string $value)
    {
        // Validate a money format: non-negative with exactly 2 decimal places
        if (preg_match('/^\d+\.\d{2}$/', $value) !== 1) {
            throw new DecimalTypeException(sprintf('Money value "%s" must be non-negative with exactly 2 decimal places', $value));
        }

        // Validate as strict decimal (will now pass since trailing zero is removed)
        self::stringToDecimal(static::moneyToDecimal($value));

        // Store the original money format with 2 decimal places
        $this->value = $value;
    }

    /**
     * @throws DecimalTypeException
     *
     * @psalm-pure
     */
    public static function fromBool(bool $value): static
    {
        return new static(static::decimalToMoney(
            static::boolToDecimal($value))
        );
    }

    /**
     * @throws DecimalTypeException
     *
     * @psalm-pure
     */
    public static function fromDecimal(string $value): static
    {
        return new static($value);
    }

    /**
     * @throws FloatTypeException
     * @throws StringTypeException
     * @throws DecimalTypeException
     *
     * @psalm-pure
     */
    public static function fromFloat(float $value): static
    {
        return new static(static::decimalToMoney(
            static::floatToDecimal($value)
        ));
    }

    /**
     * @throws DecimalTypeException
     *
     * @psalm-pure
     */
    public static function fromInt(int $value): static
    {
        return new static(static::decimalToMoney(
            static::intToDecimal($value)
        ));
    }

    /**
     * @throws DecimalTypeException
     *
     * @psalm-pure
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function isEmpty(): bool
    {
        return false;
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

    public function isUndefined(): bool
    {
        return false;
    }

    /**
     * @return non-empty-string
     */
    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * @throws StringTypeException
     */
    public function toBool(): bool
    {
        return static::stringToBool(
            static::moneyToDecimal($this->value())
        );
    }

    /**
     * @return non-empty-string
     */
    public function toDecimal(): string
    {
        return static::moneyToDecimal($this->value());
    }

    /**
     * @throws FloatTypeException
     * @throws StringTypeException
     * @throws DecimalTypeException
     */
    public function toFloat(): float
    {
        return static::stringToFloat(
            static::moneyToDecimal($this->value())
        );
    }

    /**
     * @throws DecimalTypeException
     */
    public function toInt(): int
    {
        return static::decimalToInt(
            static::moneyToDecimal($this->value())
        );
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->value();
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromBool($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromDecimal(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromFloat($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromInt($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return match (true) {
                is_string($value) => static::fromString($value),
                is_float($value) => static::fromFloat($value),
                is_int($value) => static::fromInt($value),
                //                ($value instanceof self) => static::fromString($value->value()),
                is_bool($value) => static::fromBool($value),
                $value instanceof Stringable, is_scalar($value) => static::fromString((string) $value),
                default => throw new TypeException('Value cannot be cast to string'),
            };
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @return non-empty-string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Denormalize to money format: add trailing zero
     * e.g., "0.1" -> "0.10", "1.0" -> "1.00".
     *
     * @return non-empty-string
     *
     * @psalm-pure
     */
    private static function decimalToMoney(string $value): string
    {
        if (preg_match('/^\d+\.\d$/', $value) === 1) {
            $value .= '0'; // add trailing zero
        }

        /** @var non-empty-string $value */
        return $value;
    }

    /**
     * Normalize from money format: strip trailing zero
     * e.g., "0.10" -> "0.1", "1.00" -> "1.0".
     *
     * @return non-empty-string
     *
     * @psalm-pure
     */
    private static function moneyToDecimal(string $value): string
    {
        // delete trailing zero
        /** @var non-empty-string */
        return preg_replace('/(\.\d)0$/', '$1', $value) ?? $value;
    }
}
