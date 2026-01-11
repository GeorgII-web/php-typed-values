<?php

declare(strict_types=1);

use PhpTypedValues\Exception\String\CountryCodeStringTypeException;
use PhpTypedValues\String\Alias\Specific\CountryCode;
use PhpTypedValues\String\Specific\StringCountryCode;
use PhpTypedValues\Undefined\Alias\Undefined;

it('accepts valid country code and normalizes to uppercase; preserves toString and __toString', function (): void {
    $c = new StringCountryCode('US');

    expect($c->value())
        ->toBe('US')
        ->and($c->toString())
        ->toBe('US')
        ->and((string) $c)
        ->toBe('US');
});

it('throws on malformed or unknown country codes', function (): void {
    // Wrong length/format
    expect(fn() => new StringCountryCode('U'))
        ->toThrow(CountryCodeStringTypeException::class, 'Expected ISO 3166-1 alpha-2 country code (AA), got "U"')
        ->and(fn() => StringCountryCode::fromString('123'))
        ->toThrow(CountryCodeStringTypeException::class, 'Expected ISO 3166-1 alpha-2 country code (AA), got "123"');

    // Looks like a code, but not in our allow-list
    expect(fn() => StringCountryCode::fromString('ZZ'))
        ->toThrow(CountryCodeStringTypeException::class, 'Unknown ISO 3166-1 alpha-2 country code "ZZ"');
});

it('tryFromString returns instance for valid code and Undefined for invalid/unknown', function (): void {
    $ok = StringCountryCode::tryFromString('GB');
    $bad1 = StringCountryCode::tryFromString('A');
    $bad2 = StringCountryCode::tryFromString('ZZ');

    expect($ok)
        ->toBeInstanceOf(StringCountryCode::class)
        ->and($ok->value())
        ->toBe('GB')
        ->and($bad1)
        ->toBeInstanceOf(Undefined::class)
        ->and($bad2)
        ->toBeInstanceOf(Undefined::class);
});

it('checks every code', function (): void {
    /** @var list<non-empty-string> $codes */
    $codes = [
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

    foreach ($codes as $code) {
        expect(CountryCode::fromString($code)->value())->toBe($code);
    }
});

it('explicitly accepts tail-list country codes YT, ZA, ZM (guards against element removal mutations)', function (): void {
    expect(StringCountryCode::fromString('YT')->value())->toBe('YT')
        ->and(StringCountryCode::fromString('ZA')->value())->toBe('ZA')
        ->and(StringCountryCode::fromString('ZM')->value())->toBe('ZM');
});

it('jsonSerialize returns string', function (): void {
    expect(StringCountryCode::fromString('DE')->jsonSerialize())->toBeString();
});

it('tryFromMixed handles valid country codes and invalid mixed inputs', function (): void {
    // valid uppercase string
    $ok = StringCountryCode::tryFromMixed('US');

    // stringable producing a valid code
    $stringable = new class {
        public function __toString(): string
        {
            return 'GB';
        }
    };
    $fromStringable = StringCountryCode::tryFromMixed($stringable);

    // invalid: lowercase format, unknown code, wrong types
    $badLower = StringCountryCode::tryFromMixed('us');
    $badUnknown = StringCountryCode::tryFromMixed('ZZ');
    $fromArray = StringCountryCode::tryFromMixed(['US']);
    $fromNull = StringCountryCode::tryFromMixed(null);
    $fromScalar = StringCountryCode::tryFromMixed(123); // invalid code but covers scalar check
    $fromObject = StringCountryCode::tryFromMixed(new stdClass());

    expect($ok)->toBeInstanceOf(StringCountryCode::class)
        ->and($ok->value())->toBe('US')
        ->and($fromStringable)->toBeInstanceOf(StringCountryCode::class)
        ->and($fromStringable->value())->toBe('GB')
        ->and($badLower)->toBeInstanceOf(Undefined::class)
        ->and($badUnknown)->toBeInstanceOf(Undefined::class)
        ->and($fromArray)->toBeInstanceOf(Undefined::class)
        ->and($fromNull)->toBeInstanceOf(Undefined::class)
        ->and($fromScalar)->toBeInstanceOf(Undefined::class)
        ->and($fromObject)->toBeInstanceOf(Undefined::class);
});

it('isEmpty is always false for StringCountryCode', function (): void {
    $c = new StringCountryCode('US');
    expect($c->isEmpty())->toBeFalse();
});

it('isUndefined returns false for instances and true for Undefined results', function (): void {
    // Valid instance
    $ok = StringCountryCode::fromString('US');

    // Invalid via tryFrom*: wrong case, unknown code, and non-string mixed
    $u1 = StringCountryCode::tryFromString('us');
    $u2 = StringCountryCode::tryFromString('ZZ');
    $u3 = StringCountryCode::tryFromMixed(['US']);

    expect($ok->isUndefined())->toBeFalse()
        ->and($u1->isUndefined())->toBeTrue()
        ->and($u2->isUndefined())->toBeTrue()
        ->and($u3->isUndefined())->toBeTrue();
});

it('isTypeOf returns true when class matches', function (): void {
    $v = StringCountryCode::fromString('US');
    expect($v->isTypeOf(StringCountryCode::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $v = StringCountryCode::fromString('US');
    expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $v = StringCountryCode::fromString('US');
    expect($v->isTypeOf('NonExistentClass', StringCountryCode::class, 'AnotherClass'))->toBeTrue();
});

it('covers conversions for StringCountryCode', function (): void {
    // These will throw because "true", "1.2", "123" are not valid country codes
    expect(fn() => StringCountryCode::fromBool(true))->toThrow(CountryCodeStringTypeException::class)
        ->and(fn() => StringCountryCode::fromFloat(1.2))->toThrow(CountryCodeStringTypeException::class)
        ->and(fn() => StringCountryCode::fromInt(123))->toThrow(CountryCodeStringTypeException::class);

    $v = StringCountryCode::fromString('US');
    expect(fn() => $v->toBool())->toThrow(PhpTypedValues\Exception\Integer\IntegerTypeException::class)
        ->and(fn() => $v->toFloat())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class)
        ->and(fn() => $v->toInt())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class);
});

it('tryFromBool, tryFromFloat, tryFromInt return Undefined for StringCountryCode', function (): void {
    expect(StringCountryCode::tryFromBool(true))->toBeInstanceOf(Undefined::class)
        ->and(StringCountryCode::tryFromFloat(1.2))->toBeInstanceOf(Undefined::class)
        ->and(StringCountryCode::tryFromInt(123))->toBeInstanceOf(Undefined::class);
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringCountryCodeTest extends StringCountryCode
{
    public static function fromBool(bool $value): static
    {
        throw new Exception('test');
    }

    public static function fromFloat(float $value): static
    {
        throw new Exception('test');
    }

    public static function fromInt(int $value): static
    {
        throw new Exception('test');
    }

    public static function fromString(string $value): static
    {
        throw new Exception('test');
    }
}

it('StringCountryCode::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
    expect(StringCountryCodeTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
        ->and(StringCountryCodeTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
        ->and(StringCountryCodeTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
        ->and(StringCountryCodeTest::tryFromMixed('US'))->toBeInstanceOf(Undefined::class)
        ->and(StringCountryCodeTest::tryFromString('US'))->toBeInstanceOf(Undefined::class);
});
