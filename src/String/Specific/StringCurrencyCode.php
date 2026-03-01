<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\CurrencyCodeStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function in_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_scalar;
use function is_string;
use function preg_match;
use function sprintf;

/**
 * ISO 4217 three‑letter currency code.
 *
 * Accepts a three‑letter uppercase currency code and verifies it against a
 * curated allow‑list. The value is stored as provided; callers must supply
 * uppercase input (no implicit normalization).
 *
 * Example
 *  - $c = StringCurrencyCode::fromString('USD');
 *    $c->toString(); // 'USD'
 *  - StringCurrencyCode::fromString('usd'); // throws CurrencyCodeStringTypeException
 *
 * @psalm-immutable
 */
readonly class StringCurrencyCode extends StringTypeAbstract
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws CurrencyCodeStringTypeException
     */
    public function __construct(string $value)
    {
        if (preg_match('/^[A-Z]{3}$/', $value) !== 1) {
            throw new CurrencyCodeStringTypeException(sprintf('Expected ISO 4217 currency code (AAA), got "%s"', $value));
        }

        if (!in_array($value, self::listAllowed(), true)) {
            throw new CurrencyCodeStringTypeException(sprintf('Unknown ISO 4217 currency code "%s"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws CurrencyCodeStringTypeException
     *
     * @psalm-pure
     */
    public static function fromBool(bool $value): static
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws CurrencyCodeStringTypeException
     *
     * @psalm-pure
     */
    public static function fromDecimal(string $value): static
    {
        return new static(static::decimalToString($value));
    }

    /**
     * @throws FloatTypeException
     * @throws CurrencyCodeStringTypeException
     * @throws StringTypeException
     *
     * @psalm-pure
     */
    public static function fromFloat(float $value): static
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws CurrencyCodeStringTypeException
     *
     * @psalm-pure
     */
    public static function fromInt(int $value): static
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws CurrencyCodeStringTypeException
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

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * @throws StringTypeException
     */
    public function toBool(): bool
    {
        return static::stringToBool($this->value());
    }

    /**
     * @throws DecimalTypeException
     */
    public function toDecimal(): string
    {
        return static::stringToDecimal($this->value());
    }

    /**
     * @throws StringTypeException
     * @throws FloatTypeException
     */
    public function toFloat(): float
    {
        return static::stringToFloat($this->value());
    }

    /**
     * @throws StringTypeException
     */
    public function toInt(): int
    {
        return static::stringToInt($this->value());
    }

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
            return static::fromDecimal($value);
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
                null === $value => static::fromString('null'),
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

    public function value(): string
    {
        return $this->value;
    }

    /**
     * ISO 4217 three-letter currency codes used for validation.
     *
     * @pest-mutate-ignore
     *
     * @return list<non-empty-string>
     */
    private static function listAllowed(): array
    {
        return [
            'AED', 'AFN', 'ALL', 'AMD', 'ANG', 'AOA', 'ARS', 'AUD', 'AWG', 'AZN',
            'BAM', 'BBD', 'BDT', 'BGN', 'BHD', 'BIF', 'BMD', 'BND', 'BOB', 'BRL', 'BSD', 'BTN', 'BWP', 'BYN', 'BZD',
            'CAD', 'CDF', 'CHF', 'CLP', 'CNY', 'COP', 'CRC', 'CUP', 'CVE', 'CZK',
            'DJF', 'DKK', 'DOP', 'DZD',
            'EGP', 'ERN', 'ETB', 'EUR',
            'FJD', 'FKP',
            'GBP', 'GEL', 'GHS', 'GIP', 'GMD', 'GNF', 'GTQ', 'GYD',
            'HKD', 'HNL', 'HRK', 'HTG', 'HUF',
            'IDR', 'ILS', 'INR', 'IQD', 'IRR', 'ISK',
            'JMD', 'JOD', 'JPY',
            'KES', 'KGS', 'KHR', 'KMF', 'KPW', 'KRW', 'KWD', 'KYD', 'KZT',
            'LAK', 'LBP', 'LKR', 'LRD', 'LSL', 'LYD',
            'MAD', 'MDL', 'MGA', 'MKD', 'MMK', 'MNT', 'MOP', 'MRU', 'MUR', 'MVR', 'MWK', 'MXN', 'MYR', 'MZN',
            'NAD', 'NGN', 'NIO', 'NOK', 'NPR', 'NZD',
            'OMR',
            'PAB', 'PEN', 'PGK', 'PHP', 'PKR', 'PLN', 'PYG',
            'QAR',
            'RON', 'RSD', 'RUB', 'RWF',
            'SAR', 'SBD', 'SCR', 'SDG', 'SEK', 'SGD', 'SHP', 'SLL', 'SOS', 'SRD', 'SSP', 'STN', 'SVC', 'SYP', 'SZL',
            'THB', 'TJS', 'TMT', 'TND', 'TOP', 'TRY', 'TTD', 'TWD', 'TZS',
            'UAH', 'UGX', 'USD', 'UYU', 'UZS',
            'VES', 'VND', 'VUV',
            'WST',
            'XAF', 'XCD', 'XOF', 'XPF',
            'YER',
            'ZAR', 'ZMW', 'ZWL',
        ];
    }
}
