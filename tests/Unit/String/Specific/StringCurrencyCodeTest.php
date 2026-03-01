<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\CurrencyCodeStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringCurrencyCode;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

describe('StringCurrencyCode', function () {
    it('accepts valid currency code and preserves toString and __toString', function (): void {
        $c = new StringCurrencyCode('USD');

        expect($c->value())
            ->toBe('USD')
            ->and($c->toString())
            ->toBe('USD')
            ->and((string) $c)
            ->toBe('USD');
    });

    it('throws on malformed or unknown currency codes', function (): void {
        // Wrong length/format
        expect(fn() => new StringCurrencyCode('US'))
            ->toThrow(CurrencyCodeStringTypeException::class, 'Expected ISO 4217 currency code (AAA), got "US"')
            ->and(fn() => StringCurrencyCode::fromString('1234'))
            ->toThrow(CurrencyCodeStringTypeException::class, 'Expected ISO 4217 currency code (AAA), got "1234"');

        // Looks like a code, but not in our allow-list
        expect(fn() => StringCurrencyCode::fromString('ZZZ'))
            ->toThrow(CurrencyCodeStringTypeException::class, 'Unknown ISO 4217 currency code "ZZZ"');
    });

    it('throws on lowercase currency codes', function (): void {
        expect(fn() => StringCurrencyCode::fromString('usd'))
            ->toThrow(CurrencyCodeStringTypeException::class, 'Expected ISO 4217 currency code (AAA), got "usd"')
            ->and(fn() => StringCurrencyCode::fromString('Usd'))
            ->toThrow(CurrencyCodeStringTypeException::class, 'Expected ISO 4217 currency code (AAA), got "Usd"');
    });

    it('tryFromString returns instance for valid code and Undefined for invalid/unknown', function (): void {
        $ok = StringCurrencyCode::tryFromString('EUR');
        $bad1 = StringCurrencyCode::tryFromString('EU');
        $bad2 = StringCurrencyCode::tryFromString('ZZZ');
        $bad3 = StringCurrencyCode::tryFromString('eur');

        expect($ok)
            ->toBeInstanceOf(StringCurrencyCode::class)
            ->and($ok->value())
            ->toBe('EUR')
            ->and($bad1)
            ->toBeInstanceOf(Undefined::class)
            ->and($bad2)
            ->toBeInstanceOf(Undefined::class)
            ->and($bad3)
            ->toBeInstanceOf(Undefined::class);
    });

    it('checks every currency code', function (): void {
        /** @var list<non-empty-string> $codes */
        $codes = [
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

        foreach ($codes as $code) {
            expect(StringCurrencyCode::fromString($code)->value())->toBe($code);
        }
    });

    it('explicitly accepts tail-list currency codes ZAR, ZMW, ZWL (guards against element removal mutations)', function (): void {
        expect(StringCurrencyCode::fromString('ZAR')->value())->toBe('ZAR')
            ->and(StringCurrencyCode::fromString('ZMW')->value())->toBe('ZMW')
            ->and(StringCurrencyCode::fromString('ZWL')->value())->toBe('ZWL');
    });

    it('accepts common currency codes', function (string $code): void {
        $currency = StringCurrencyCode::fromString($code);
        expect($currency->value())->toBe($code);
    })->with([
        'USD', 'EUR', 'GBP', 'JPY', 'CHF', 'CAD', 'AUD', 'CNY', 'HKD', 'NZD',
        'SEK', 'NOK', 'DKK', 'TRY', 'RUB', 'BRL', 'INR', 'KRW', 'MXN', 'IDR',
    ]);

    it('jsonSerialize returns string', function (): void {
        expect(StringCurrencyCode::fromString('USD')->jsonSerialize())->toBeString()->toBe('USD');
    });

    it('tryFromMixed handles valid currency codes and invalid mixed inputs', function (): void {
        // valid uppercase string
        $ok = StringCurrencyCode::tryFromMixed('USD');

        // stringable producing a valid code
        $stringable = new class {
            public function __toString(): string
            {
                return 'EUR';
            }
        };
        $fromStringable = StringCurrencyCode::tryFromMixed($stringable);

        // invalid: lowercase format, unknown code, wrong types
        $badLower = StringCurrencyCode::tryFromMixed('usd');
        $badUnknown = StringCurrencyCode::tryFromMixed('ZZZ');
        $fromArray = StringCurrencyCode::tryFromMixed(['USD']);
        $fromNull = StringCurrencyCode::tryFromMixed(null);
        $fromScalar = StringCurrencyCode::tryFromMixed(123); // invalid code but covers scalar check
        $fromObject = StringCurrencyCode::tryFromMixed(new stdClass());

        expect($ok)->toBeInstanceOf(StringCurrencyCode::class)
            ->and($ok->value())->toBe('USD')
            ->and($fromStringable)->toBeInstanceOf(StringCurrencyCode::class)
            ->and($fromStringable->value())->toBe('EUR')
            ->and($badLower)->toBeInstanceOf(Undefined::class)
            ->and($badUnknown)->toBeInstanceOf(Undefined::class)
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(Undefined::class)
            ->and($fromScalar)->toBeInstanceOf(Undefined::class)
            ->and($fromObject)->toBeInstanceOf(Undefined::class);
    });

    it('isEmpty is always false for StringCurrencyCode', function (): void {
        $c = new StringCurrencyCode('USD');
        expect($c->isEmpty())->toBeFalse();
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        // Valid instance
        $ok = StringCurrencyCode::fromString('USD');

        // Invalid via tryFrom*: wrong case, unknown code, and non-string mixed
        $u1 = StringCurrencyCode::tryFromString('usd');
        $u2 = StringCurrencyCode::tryFromString('ZZZ');
        $u3 = StringCurrencyCode::tryFromMixed(['USD']);

        expect($ok->isUndefined())->toBeFalse()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue()
            ->and($u3->isUndefined())->toBeTrue();
    });

    it('round-trip conversion preserves value: string → object → string', function (): void {
        $original = 'CHF';
        $currency = StringCurrencyCode::fromString($original);
        $str = $currency->toString();

        expect($str)->toBe($original);
    });

    it('value method returns the same as toString', function (): void {
        $currency = new StringCurrencyCode('AUD');
        expect($currency->value())->toBe($currency->toString());
    });

    it('__toString magic method works correctly', function (): void {
        $currency = new StringCurrencyCode('JPY');
        expect((string) $currency)->toBe('JPY')
            ->and($currency . ' currency')->toBe('JPY currency');
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringCurrencyCode::fromString('USD');
        expect($v->isTypeOf(StringCurrencyCode::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringCurrencyCode::fromString('USD');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function (): void {
        $v = StringCurrencyCode::fromString('USD');
        expect($v->isTypeOf('NonExistentClass', StringCurrencyCode::class, 'AnotherClass'))->toBeTrue();
    });

    it('covers conversions for StringCurrencyCode', function (): void {
        // These throw because "true", "1.2", "123" are not valid currency codes
        expect(fn() => StringCurrencyCode::fromBool(true))->toThrow(CurrencyCodeStringTypeException::class)
            ->and(fn() => StringCurrencyCode::fromFloat(1.2))->toThrow(CurrencyCodeStringTypeException::class)
            ->and(fn() => StringCurrencyCode::fromInt(123))->toThrow(CurrencyCodeStringTypeException::class)
            ->and(fn() => StringCurrencyCode::fromDecimal('1.0'))->toThrow(CurrencyCodeStringTypeException::class);

        $v = StringCurrencyCode::fromString('USD');
        expect(fn() => $v->toBool())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt, tryFromDecimal return Undefined for StringCurrencyCode', function (): void {
        expect(StringCurrencyCode::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringCurrencyCode::tryFromFloat(1.2))->toBeInstanceOf(Undefined::class)
            ->and(StringCurrencyCode::tryFromInt(123))->toBeInstanceOf(Undefined::class)
            ->and(StringCurrencyCode::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringCurrencyCodeTest extends StringCurrencyCode
{
    public static function fromBool(bool $value): static
    {
        throw new Exception('Trigger fromBool');
    }

    public static function fromDecimal(string $value): static
    {
        throw new Exception('Trigger fromDecimal');
    }

    public static function fromFloat(float $value): static
    {
        throw new Exception('Trigger fromFloat');
    }

    public static function fromInt(int $value): static
    {
        throw new Exception('Trigger fromInt');
    }

    public static function fromString(string $value): static
    {
        if ($value === 'null') {
            return new self('USD');
        }

        if ($value === 'trigger-exception') {
            throw new Exception('Trigger fromString');
        }

        return new self('EUR');
    }
}

describe('Coverage for mutants', function () {
    it('tryFromMixed specifically triggers fromString("null") for null value', function (): void {
        $result = StringCurrencyCodeTest::tryFromMixed(null);
        expect($result)->toBeInstanceOf(StringCurrencyCode::class)
            ->and($result->value())->toBe('USD');
    });

    it('tryFromMixed specifically triggers default branch for unknown types like array', function (): void {
        $result = StringCurrencyCodeTest::tryFromMixed([]);
        expect($result)->toBeInstanceOf(Undefined::class);
    });

    it('tryFrom* methods return default on exception', function (): void {
        $default = new Undefined();

        expect(StringCurrencyCodeTest::tryFromBool(true, $default))->toBe($default)
            ->and(StringCurrencyCodeTest::tryFromDecimal('1.23', $default))->toBe($default)
            ->and(StringCurrencyCodeTest::tryFromFloat(1.23, $default))->toBe($default)
            ->and(StringCurrencyCodeTest::tryFromInt(123, $default))->toBe($default)
            ->and(StringCurrencyCodeTest::tryFromString('trigger-exception', $default))->toBe($default);
    });
});
