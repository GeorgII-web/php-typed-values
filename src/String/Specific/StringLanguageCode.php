<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Bool\BoolTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\LanguageCodeStringTypeException;
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
 * ISO 639-1 two‑letter language code.
 *
 * Accepts a two‑letter lowercase language code and verifies it against a
 * curated allow‑list. The value is stored as provided; callers must supply
 * lowercase input (no implicit normalization).
 *
 * Example
 *  - $l = StringLanguageCode::fromString('en');
 *    $l->toString(); // 'en'
 *  - StringLanguageCode::fromString('EN'); // throws LanguageCodeStringTypeException
 *
 * @psalm-immutable
 */
readonly class StringLanguageCode extends StringTypeAbstract
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws LanguageCodeStringTypeException
     */
    public function __construct(string $value)
    {
        if (preg_match('/^[a-z]{2}$/', $value) !== 1) {
            throw new LanguageCodeStringTypeException(sprintf('Expected ISO 639-1 language code (aa), got "%s"', $value));
        }

        if (!in_array($value, self::listAllowed(), true)) {
            throw new LanguageCodeStringTypeException(sprintf('Unknown ISO 639-1 language code "%s"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws LanguageCodeStringTypeException
     */
    public static function fromBool(bool $value): static
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws FloatTypeException
     * @throws LanguageCodeStringTypeException
     */
    public static function fromFloat(float $value): static
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws FloatTypeException
     * @throws LanguageCodeStringTypeException
     */
    public static function fromInt(int $value): static
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws LanguageCodeStringTypeException
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
     * @throws BoolTypeException
     */
    public function toBool(): bool
    {
        return static::stringToBool($this->value());
    }

    /**
     * @throws FloatTypeException
     */
    public function toFloat(): float
    {
        return static::stringToFloat($this->value());
    }

    /**
     * @throws IntegerTypeException
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

    /** @return non-empty-string */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * ISO 639-1 two-letter language codes used for validation.
     *
     * @pest-mutate-ignore
     *
     * @return list<non-empty-string>
     */
    private static function listAllowed(): array
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
