<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\LocaleStringTypeException;
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
 * Locale code in the format `language_COUNTRY`.
 *
 * Accepts locale codes in the format `ll_RR` where:
 *  - `ll` is a lowercase ISO 639-1 two-letter language code
 *  - `CC` is an uppercase ISO 3166-1 alpha-2 country code
 *
 * Example
 *  - $l = StringLocaleCode::fromString('de_DE');
 *    $l->toString(); // 'de_DE'
 *  - StringLocaleCode::fromString('de_de'); // throws LocaleStringTypeException
 *
 * @psalm-immutable
 */
class StringLocaleCode extends StringTypeAbstract
{
    /** @var non-empty-string
     * @readonly */
    protected string $value;

    /** @var non-empty-string
     * @readonly */
    private string $country;

    /** @var non-empty-string
     * @readonly */
    private string $language;

    /**
     * @throws LocaleStringTypeException
     */
    public function __construct(string $value)
    {
        if (preg_match('/^([a-z]{2})_([A-Z]{2})$/', $value, $matches) !== 1) {
            throw new LocaleStringTypeException(sprintf('Expected locale code (ll_RR), got "%s"', $value));
        }

        $language = $matches[1];
        $country = $matches[2];

        if (!in_array($language, self::listAllowedLanguages(), true)) {
            throw new LocaleStringTypeException(sprintf('Unknown ISO 639-1 language code "%s"', $language));
        }

        if (!in_array($country, self::listAllowedCountries(), true)) {
            throw new LocaleStringTypeException(sprintf('Unknown ISO 3166-1 country code "%s"', $country));
        }

        /** @var non-empty-string $value */
        $this->value = $value;

        $this->language = $language;
        $this->country = $country;
    }

    /**
     * @throws StringTypeException
     * @throws LocaleStringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws LocaleStringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromDecimal(string $value)
    {
        return new static(static::decimalToString($value));
    }

    /**
     * @throws FloatTypeException
     * @throws LocaleStringTypeException
     * @throws StringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromFloat(float $value)
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws LocaleStringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws LocaleStringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static($value);
    }

    /**
     * Get the country/region part of the locale.
     *
     * @return non-empty-string ISO 3166-1 country code
     */
    public function getCountryCode(): string
    {
        return $this->country;
    }

    /**
     * Get the language part of the locale.
     *
     * @return non-empty-string ISO 639-1 language code
     */
    public function getLanguageCode(): string
    {
        return $this->language;
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
     * @throws FloatTypeException
     * @throws StringTypeException
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
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromBool($value);
        } catch (Exception $exception) {
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
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromDecimal($value);
        } catch (Exception $exception) {
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
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromFloat($value);
        } catch (Exception $exception) {
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
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromInt($value);
        } catch (Exception $exception) {
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
     * @param mixed $value
     */
    public static function tryFromMixed(
        $value,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            switch (true) {
                case is_string($value):
                    return static::fromString($value);
                case is_float($value):
                    return static::fromFloat($value);
                case is_int($value):
                    return static::fromInt($value);
                case is_bool($value):
                    return static::fromBool($value);
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
        PrimitiveTypeAbstract $default = null
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

    /** @return non-empty-string */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * ISO 3166-1 alpha-2 country codes used for validation.
     *
     * @pest-mutate-ignore
     *
     * @return list<non-empty-string>
     */
    private static function listAllowedCountries(): array
    {
        return [
            'AD', 'AE', 'AF', 'AG', 'AI', 'AL', 'AM', 'AO', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AW', 'AX', 'AZ',
            'BA', 'BB', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BL', 'BM', 'BN', 'BO', 'BQ', 'BR', 'BS', 'BT', 'BV', 'BW', 'BY', 'BZ',
            'CA', 'CC', 'CD', 'CF', 'CG', 'CH', 'CI', 'CK', 'CL', 'CM', 'CN', 'CO', 'CR', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
            'DE', 'DJ', 'DK', 'DM', 'DO', 'DZ',
            'EC', 'EE', 'EG', 'EH', 'ER', 'ES', 'ET',
            'FI', 'FJ', 'FK', 'FM', 'FO', 'FR',
            'GA', 'GB', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GL', 'GM', 'GN', 'GP', 'GQ', 'GR', 'GS', 'GT', 'GU', 'GW', 'GY',
            'HK', 'HM', 'HN', 'HR', 'HT', 'HU',
            'ID', 'IE', 'IL', 'IM', 'IN', 'IO', 'IQ', 'IR', 'IS', 'IT',
            'JE', 'JM', 'JO', 'JP',
            'KE', 'KG', 'KH', 'KI', 'KM', 'KN', 'KP', 'KR', 'KW', 'KY', 'KZ',
            'LA', 'LB', 'LC', 'LI', 'LK', 'LR', 'LS', 'LT', 'LU', 'LV', 'LY',
            'MA', 'MC', 'MD', 'ME', 'MF', 'MG', 'MH', 'MK', 'ML', 'MM', 'MN', 'MO', 'MP', 'MQ', 'MR', 'MS', 'MT', 'MU', 'MV', 'MW', 'MX', 'MY', 'MZ',
            'NA', 'NC', 'NE', 'NF', 'NG', 'NI', 'NL', 'NO', 'NP', 'NR', 'NU', 'NZ',
            'OM',
            'PA', 'PE', 'PF', 'PG', 'PH', 'PK', 'PL', 'PM', 'PN', 'PR', 'PS', 'PT', 'PW', 'PY',
            'QA',
            'RE', 'RO', 'RS', 'RU', 'RW',
            'SA', 'SB', 'SC', 'SD', 'SE', 'SG', 'SH', 'SI', 'SJ', 'SK', 'SL', 'SM', 'SN', 'SO', 'SR', 'SS', 'ST', 'SV', 'SX', 'SY', 'SZ',
            'TC', 'TD', 'TF', 'TG', 'TH', 'TJ', 'TK', 'TL', 'TM', 'TN', 'TO', 'TR', 'TT', 'TV', 'TW', 'TZ',
            'UA', 'UG', 'UM', 'US', 'UY', 'UZ',
            'VA', 'VC', 'VE', 'VG', 'VI', 'VN', 'VU',
            'WF', 'WS',
            'YE', 'YT',
            'ZA', 'ZM', 'ZW',
        ];
    }

    /**
     * Common ISO 639-1 language codes used for validation.
     *
     * @pest-mutate-ignore
     *
     * @return list<non-empty-string>
     */
    private static function listAllowedLanguages(): array
    {
        return [
            'aa', 'ab', 'ae', 'af', 'ak', 'am', 'an', 'ar', 'as', 'av', 'ay', 'az',
            'ba', 'be', 'bg', 'bh', 'bi', 'bm', 'bn', 'bo', 'br', 'bs',
            'ca', 'ce', 'ch', 'co', 'cr', 'cs', 'cu', 'cv', 'cy',
            'da', 'de', 'dv', 'dz',
            'ee', 'el', 'en', 'eo', 'es', 'et', 'eu',
            'fa', 'ff', 'fi', 'fj', 'fo', 'fr', 'fy',
            'ga', 'gd', 'gl', 'gn', 'gu', 'gv',
            'ha', 'he', 'hi', 'ho', 'hr', 'ht', 'hu', 'hy', 'hz',
            'ia', 'id', 'ie', 'ig', 'ii', 'ik', 'io', 'is', 'it', 'iu',
            'ja', 'jv',
            'ka', 'kg', 'ki', 'kj', 'kk', 'kl', 'km', 'kn', 'ko', 'kr', 'ks', 'ku', 'kv', 'kw', 'ky',
            'la', 'lb', 'lg', 'li', 'ln', 'lo', 'lt', 'lu', 'lv',
            'mg', 'mh', 'mi', 'mk', 'ml', 'mn', 'mr', 'ms', 'mt', 'my',
            'na', 'nb', 'nd', 'ne', 'ng', 'nl', 'nn', 'no', 'nr', 'nv', 'ny',
            'oc', 'oj', 'om', 'or', 'os',
            'pa', 'pi', 'pl', 'ps', 'pt',
            'qu',
            'rm', 'rn', 'ro', 'ru', 'rw',
            'sa', 'sc', 'sd', 'se', 'sg', 'si', 'sk', 'sl', 'sm', 'sn', 'so', 'sq', 'sr', 'ss', 'st', 'su', 'sv', 'sw',
            'ta', 'te', 'tg', 'th', 'ti', 'tk', 'tl', 'tn', 'to', 'tr', 'ts', 'tt', 'tw', 'ty',
            'ug', 'uk', 'ur', 'uz',
            've', 'vi', 'vo',
            'wa', 'wo',
            'xh',
            'yi', 'yo',
            'za', 'zh', 'zu',
        ];
    }
}
