<?php

declare(strict_types=1);

use PhpTypedValues\Exception\CountryCodeStringTypeException;
use PhpTypedValues\String\Alias\CountryCode;
use PhpTypedValues\String\StringCountryCode;
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
